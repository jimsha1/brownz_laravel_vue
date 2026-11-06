<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\BookingEditLog;
use App\Models\BookingItem;
use App\Models\BookingPayment;
use App\Models\BookingService;
use App\Models\SplitServiceAmount;
use App\Service\AccountingService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ApplyServiceDiscountCommand extends Command
{
    protected $signature = 'booking:apply-service-discount
                            {booking : Booking code or booking id}
                            {--service= : Service name on this booking}
                            {--service-id= : booking_service_id when the name matches more than one line}
                            {--item= : Product name on this booking}
                            {--item-id= : booking_item_id when the name matches more than one line}
                            {--amount= : Discount in rupees. GST stays on the original price, so the refund equals this amount}
                            {--payment-id= : Payment that should carry the refund when the booking has more than one payment}
                            {--execute : Write the discount, refund, and accounts. Without this flag the command only prints the change}';

    protected $description = 'Apply a rupee discount on one service or product, keep GST on the original price, and update the invoice, refund, and accounts.';

    public function handle(): int
    {
        $execute = (bool) $this->option('execute');
        $amount = $this->moneyOption('amount');

        if ($amount === null || $amount <= 0) {
            $this->error('Pass the discount with --amount, for example --amount=2000.');

            return self::FAILURE;
        }

        $booking = $this->findBooking((string) $this->argument('booking'));
        if (! $booking) {
            $this->error('Booking was not found.');

            return self::FAILURE;
        }

        if ($booking->booking_type !== Booking::BOOKING_TYPE_SALE) {
            $this->error('Booking '.$booking->booking_code.' is a '.$booking->booking_type.'. This command only updates a sale.');

            return self::FAILURE;
        }

        $booking->load(['bookingOnlyServices', 'bookingItems', 'payments']);

        $target = $this->resolveTarget($booking);
        if (! is_array($target)) {
            return (int) $target;
        }

        /** @var BookingService|BookingItem $line */
        $line = $target['line'];
        $kind = $target['kind'];
        $gross = $target['gross'];
        $taxPercent = $target['tax_percent'];
        $label = $target['label'];

        if (round((float) $line->discount_amount, 2) > 0) {
            $this->warn('This line also has a percentage discount of '.$this->money($line->discount_amount).'. It will be replaced by the rupee discount so the two are not both counted.');
        }

        if ($amount > $gross) {
            $this->error('Discount '.$this->money($amount).' is larger than the line price '.$this->money($gross).'.');

            return self::FAILURE;
        }

        $taxAmount = round($gross * ($taxPercent / 100), 2);
        $lineTotal = round($gross - $amount + $taxAmount, 2);
        $discountPercent = $gross > 0 ? round(($amount / $gross) * 100, 2) : 0.0;
        $expectedTotal = $this->expectedBookingTotal($booking, $kind, $line, $lineTotal);

        $oldTotal = round((float) $booking->booking_total, 2);
        $delta = round($oldTotal - $expectedTotal, 2);
        $paymentPlan = $this->planPayment($booking, $delta);

        if (isset($paymentPlan['error'])) {
            $this->error($paymentPlan['error']);
            if (! empty($paymentPlan['rows'])) {
                $this->table(['Payment', 'Mode', 'Taken', 'Change', 'Net'], $paymentPlan['rows']);
            }

            return self::FAILURE;
        }

        $this->info('Invoice '.($booking->booking_code ?: '-').' / booking '.$booking->booking_id.' / '.$booking->client_name);
        $this->line($label);
        $this->table(
            ['', 'Now', 'After'],
            [
                ['Discount', $this->money($line->individual_discount_amt), $this->money($amount).' ('.$this->money($discountPercent).'%)'],
                ['GST '.$this->money($taxPercent).'%', $this->money($line->tax_amount), $this->money($taxAmount)],
                ['Line total', $this->money($line->line_total), $this->money($lineTotal)],
                ['Invoice total', $this->money($oldTotal), $this->money($expectedTotal)],
                ['Refund on this payment', $paymentPlan['refund_now'], $paymentPlan['refund_after']],
            ]
        );
        $alreadyApplied = round((float) $line->individual_discount_amt, 2) === $amount
            && round((float) $line->tax_amount, 2) === $taxAmount
            && round((float) $line->line_total, 2) === $lineTotal
            && $oldTotal === $expectedTotal
            && $paymentPlan['already'];

        $this->line('GST stays on the original price of '.$this->money($gross).'. Other lines and the round-off of '.$this->money($booking->applied_roundoff_price).' stay as they are.');
        $this->line($paymentPlan['note']);
        if (! $alreadyApplied && ! empty($paymentPlan['accounts'])) {
            $this->line($paymentPlan['accounts']);
        }

        if ($alreadyApplied) {
            $this->info('Already applied. Nothing to change.');

            return self::SUCCESS;
        }

        if (! $execute) {
            $this->warn('Preview only. Run the same command with --execute to write these changes.');

            return self::SUCCESS;
        }

        DB::transaction(function () use (
            $booking,
            $line,
            $kind,
            $amount,
            $discountPercent,
            $taxAmount,
            $lineTotal,
            $expectedTotal,
            $paymentPlan,
            $oldTotal,
            $label,
            $taxPercent,
            $gross
        ) {
            $line->individual_discount_percent = $discountPercent;
            $line->individual_discount_amt = $amount;
            $line->discount_percent = 0;
            $line->discount_amount = 0;
            $line->tax_amount = $taxAmount;
            $line->line_total = $lineTotal;
            $line->save();

            $this->syncSplitAmounts($kind, $line, $lineTotal);

            $booking->refresh();
            $booking->load(['bookingOnlyServices', 'bookingItems']);
            $booking->resetBookingAmount();

            if ($paymentPlan['payment'] instanceof BookingPayment) {
                $paymentPlan['payment']->amount_given = $paymentPlan['new_given'];
                $paymentPlan['payment']->save();
            }

            $booking->refresh();
            $booking->load('payments');
            $booking->resetPaymentTotal();

            if (abs((float) $booking->booking_total - $expectedTotal) >= 0.01) {
                throw new RuntimeException('Booking total is '.$booking->booking_total.', expected '.$this->money($expectedTotal).'.');
            }

            if ($paymentPlan['payment'] instanceof BookingPayment
                && abs((float) $booking->payment_total - $expectedTotal) >= 0.01) {
                throw new RuntimeException('Payment total is '.$booking->payment_total.', expected '.$this->money($expectedTotal).'.');
            }

            BookingEditLog::create([
                'booking_id' => $booking->booking_id,
                'edit_reason' => sprintf(
                    '₹%s discount on %s. GST kept at %s%% of ₹%s. Invoice ₹%s to ₹%s. Refund ₹%s.',
                    $this->money($amount),
                    $label,
                    $this->money($taxPercent),
                    $this->money($gross),
                    $this->money($oldTotal),
                    $this->money($expectedTotal),
                    $this->money($paymentPlan['new_given'] ?? 0)
                ),
                'edited_by' => null,
            ]);

            if ($booking->booking_code
                && abs((float) $booking->booking_total - (float) $booking->payment_total) < 0.01
                && (float) $booking->payment_total > 0) {
                $booking->load(['bookingOnlyServices', 'bookingItems', 'payments']);
                app(AccountingService::class)->resyncSalonBookingAccounting($booking);
            }
        });

        $booking->refresh();
        $line->refresh();
        $this->info('Applied. Invoice total '.$this->money($booking->booking_total).', payment '.$this->money($booking->payment_total).', status '.$booking->payment_status.'.');
        $this->line('Line discount '.$this->money($line->individual_discount_amt).', GST '.$this->money($line->tax_amount).', line '.$this->money($line->line_total).'.');

        return self::SUCCESS;
    }

    private function findBooking(string $key): ?Booking
    {
        $key = trim($key);

        if ($key !== '' && ctype_digit($key)) {
            $byId = Booking::find((int) $key);
            if ($byId) {
                return $byId;
            }
        }

        return Booking::where('booking_code', $key)->first();
    }

    /**
     * @return array{kind: string, line: BookingService|BookingItem, gross: float, tax_percent: float, label: string}|int
     */
    private function resolveTarget(Booking $booking): array|int
    {
        $serviceName = $this->option('service');
        $serviceId = $this->option('service-id');
        $itemName = $this->option('item');
        $itemId = $this->option('item-id');
        $wantsService = ($serviceName !== null && $serviceName !== '') || ($serviceId !== null && $serviceId !== '');
        $wantsItem = ($itemName !== null && $itemName !== '') || ($itemId !== null && $itemId !== '');

        if ($wantsService === $wantsItem) {
            $this->error('Pass either a service (--service or --service-id) or a product (--item or --item-id).');

            return self::FAILURE;
        }

        if ($wantsService) {
            $matches = $this->matchLines($booking->bookingOnlyServices, 'booking_service_id', $serviceId, $serviceName, 'service_name');
            $nameColumn = 'service_name';
            $kind = 'service';
            $idLabel = 'booking_service_id';
        } else {
            $matches = $this->matchLines($booking->bookingItems, 'booking_item_id', $itemId, $itemName, 'item_name');
            $nameColumn = 'item_name';
            $kind = 'product';
            $idLabel = 'booking_item_id';
        }

        if ($matches->count() !== 1) {
            if ($matches->isEmpty()) {
                $this->error('No matching '.$kind.' line on booking '.($booking->booking_code ?: $booking->booking_id).'.');
            } else {
                $this->error('More than one '.$kind.' line matches. Pass --'.($kind === 'service' ? 'service-id' : 'item-id').'.');
            }

            $rows = $matches->isEmpty()
                ? ($kind === 'service' ? $booking->bookingOnlyServices : $booking->bookingItems)
                : $matches;

            $this->table(
                [$idLabel, 'Name', 'Qty', 'Price', 'Discount', 'GST', 'Line'],
                $rows->map(function ($row) use ($kind, $idLabel) {
                    $qty = (float) ($row->quantity ?: 1);
                    $unit = $kind === 'service' ? (float) $row->service_booking_price : (float) $row->selling_price;

                    return [
                        $row->{$idLabel},
                        $row->{$kind === 'service' ? 'service_name' : 'item_name'},
                        $qty,
                        $this->money($unit),
                        $this->money($row->individual_discount_amt),
                        $this->money($row->tax_amount),
                        $this->money($row->line_total),
                    ];
                })->all()
            );

            return self::FAILURE;
        }

        $line = $matches->first();
        $qty = (float) ($line->quantity ?: 1);
        $unit = $kind === 'service' ? (float) $line->service_booking_price : (float) $line->selling_price;

        return [
            'kind' => $kind,
            'line' => $line,
            'gross' => round($unit * $qty, 2),
            'tax_percent' => round((float) $line->tax_percent, 2),
            'label' => trim((string) $line->{$nameColumn}),
        ];
    }

    private function matchLines(Collection $rows, string $idColumn, mixed $id, mixed $name, string $nameColumn): Collection
    {
        if ($id !== null && $id !== '') {
            return $rows->where($idColumn, (int) $id)->values();
        }

        $needle = trim((string) $name);

        return $rows->filter(function ($row) use ($needle, $nameColumn) {
            return strcasecmp(trim((string) $row->{$nameColumn}), $needle) === 0;
        })->values();
    }

    private function expectedBookingTotal(Booking $booking, string $kind, BookingService|BookingItem $line, float $lineTotal): float
    {
        $services = 0.0;
        foreach ($booking->bookingOnlyServices as $row) {
            $services += ($kind === 'service' && (int) $row->booking_service_id === (int) $line->booking_service_id)
                ? $lineTotal
                : (float) $row->line_total;
        }

        $items = 0.0;
        foreach ($booking->bookingItems as $row) {
            $items += ($kind === 'product' && (int) $row->booking_item_id === (int) $line->booking_item_id)
                ? $lineTotal
                : (float) $row->line_total;
        }

        return round(
            $services
            + $items
            - (float) ($booking->applied_discount_price ?? 0)
            - (float) ($booking->applied_roundoff_price ?? 0),
            2
        );
    }

    /**
     * @return array{payment: ?BookingPayment, new_given: ?float, already: bool, refund_now: string, refund_after: string, note: string, error?: string, rows?: array}
     */
    private function planPayment(Booking $booking, float $delta): array
    {
        $payments = $booking->payments->values();
        $settled = abs(round((float) $booking->payment_total, 2) - round((float) $booking->booking_total, 2)) < 0.01
            && (float) $booking->payment_total > 0;
        $rows = $payments->map(function (BookingPayment $payment) {
            $net = round((float) $payment->amount_taken - (float) $payment->amount_given, 2);

            return [
                $payment->payment_id,
                $payment->payment_mode,
                $this->money($payment->amount_taken),
                $this->money($payment->amount_given),
                $this->money($net),
            ];
        })->all();

        if (! $settled) {
            return [
                'payment' => null,
                'new_given' => null,
                'already' => true,
                'refund_now' => '-',
                'refund_after' => '-',
                'note' => 'This invoice is not fully paid, so the payment rows stay as they are. Accounts are updated only when the payment matches the new total.',
            ];
        }

        $paymentId = $this->option('payment-id');
        if ($payments->count() > 1 && ($paymentId === null || $paymentId === '')) {
            return [
                'error' => 'This paid invoice has more than one payment. Pass --payment-id for the payment that should carry the refund.',
                'rows' => $rows,
                'payment' => null,
                'new_given' => null,
                'already' => false,
                'refund_now' => '-',
                'refund_after' => '-',
                'note' => '',
            ];
        }

        $payment = ($paymentId !== null && $paymentId !== '')
            ? $payments->firstWhere('payment_id', (int) $paymentId)
            : $payments->first();

        if (! $payment) {
            return [
                'error' => 'Payment '.$paymentId.' is not on this booking.',
                'rows' => $rows,
                'payment' => null,
                'new_given' => null,
                'already' => false,
                'refund_now' => '-',
                'refund_after' => '-',
                'note' => '',
            ];
        }

        if (! empty($payment->change_as_credit) || ! empty($payment->prepaid_applied)) {
            return [
                'error' => 'Payment '.$payment->payment_id.' is client credit, so the refund cannot be recorded as change on that row.',
                'payment' => null,
                'new_given' => null,
                'already' => false,
                'refund_now' => '-',
                'refund_after' => '-',
                'note' => '',
            ];
        }

        $newGiven = round((float) $payment->amount_given + $delta, 2);
        $taken = round((float) $payment->amount_taken, 2);

        if ($newGiven < 0 || $newGiven > $taken) {
            return [
                'error' => 'Refund '.$this->money($newGiven).' does not fit payment '.$payment->payment_id.' (taken '.$this->money($taken).').',
                'payment' => null,
                'new_given' => null,
                'already' => false,
                'refund_now' => '-',
                'refund_after' => '-',
                'note' => '',
            ];
        }

        $accounts = $booking->booking_code
            ? 'On execute, accounts for '.$booking->booking_code.' are reversed and posted again.'
            : 'This booking has no invoice code, so accounts will not be posted.';

        return [
            'payment' => $payment,
            'new_given' => $newGiven,
            'already' => abs($newGiven - round((float) $payment->amount_given, 2)) < 0.01,
            'refund_now' => $this->money($payment->amount_given),
            'refund_after' => $this->money($newGiven),
            'note' => 'Card or cash taken stays '.$this->money($taken).'. The refund recorded as change becomes '.$this->money($newGiven).'.',
            'accounts' => $accounts,
        ];
    }

    private function syncSplitAmounts(string $kind, BookingService|BookingItem $line, float $lineTotal): void
    {
        $column = $kind === 'service' ? 'booking_service_id' : 'booking_item_id';
        $lineId = $kind === 'service' ? $line->booking_service_id : $line->booking_item_id;
        $splits = SplitServiceAmount::where($column, $lineId)->orderBy('split_service_amount_id')->get();

        if ($splits->isEmpty()) {
            return;
        }

        $oldSum = round((float) $splits->sum('price'), 2);
        if ($splits->count() > 1 && $oldSum <= 0) {
            throw new RuntimeException('Split amounts for this line cannot be scaled because their prices are zero.');
        }

        $allocated = 0.0;
        $lastIndex = $splits->count() - 1;

        foreach ($splits->values() as $index => $row) {
            if ($splits->count() === 1) {
                $share = $lineTotal;
            } elseif ($index === $lastIndex) {
                $share = round($lineTotal - $allocated, 2);
            } else {
                $share = round($lineTotal * ((float) $row->price) / $oldSum, 2);
                $allocated += $share;
            }

            $row->price = $share;
            $row->total_amount = $lineTotal;
            if ((float) $row->commission_percentage > 0) {
                $row->commission_amount = round($share * ((float) $row->commission_percentage) / 100, 2);
            }
            $row->save();
        }
    }

    private function moneyOption(string $name): ?float
    {
        $value = $this->option($name);
        if ($value === null || $value === '') {
            return null;
        }

        $normalized = str_replace(',', '', (string) $value);
        if (! is_numeric($normalized)) {
            return null;
        }

        return round((float) $normalized, 2);
    }

    private function money(mixed $amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }
}
