<?php

namespace App\Http\Controllers\Salon\BookingDetails;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\BookingRepository;
use App\Models\Booking;
use App\Models\BusinessConfig;
use App\Models\Service;
use App\Models\StockItem;
use App\Models\User;
use App\Models\WhatsAppInvoice;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Auth;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\Cloner\Data;
use Illuminate\Support\Facades\Log;
use niklasravnsborg\LaravelPdf\Facades\Pdf as mPDF;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class InvoiceController extends Controller
{
    public function index(Request $request, BookingRepository $booking_repo, $booking_id, $gift = null)
    {

        $seller_name = "";
        $seller_vat = "";
        $officeAddress = $users = DB::table('business_configs')->whereIn('config_key', array('business_title_english', 'business_title_arabic', 'vat_registration_number', 'web_address', 'footer_english', 'footer_arabic'))->get();

        foreach ($officeAddress as $data) {
            if ($data->config_key == 'business_title_english') {
                $seller_name = $data->config_value;
            }
            if ($data->config_key == 'vat_registration_number') {

                $seller_vat = $data->config_value;
            }
        }
        $booking_info = $this->findById($booking_id, $booking_repo);
        $amountToPay = $booking_info->payment_total;

        $booking_info = $this->findById($booking_id, $booking_repo);
        // dd($booking_info->bookingOnlyServices);
        $calculations = [
            'plain_net_total' => 0,
            'discount_total' => 0,
            'tax_total' => 0,
        ];

        if (count($booking_info->bookingOnlyServices) > 0) {
            foreach ($booking_info->bookingOnlyServices as $service) {
                $qty = (float) ($service->quantity ?: 1);
                $calculations['plain_net_total'] += ((float) $service->service_booking_price) * $qty;
                $lineDiscount = ((float) ($service->individual_discount_amt ?? 0) != 0.0)
                    ? (float) $service->individual_discount_amt
                    : (float) ($service->discount_amount ?? 0);
                $calculations['discount_total'] += $lineDiscount;
                $calculations['tax_total'] += (float) ($service->tax_amount ?? 0);
            }
        }
        if (count($booking_info->bookingItems) > 0) {
            foreach ($booking_info->bookingItems as $item) {
                $qty = (float) ($item->quantity ?: 1);
                $calculations['plain_net_total'] += ((float) $item->selling_price) * $qty;
                $lineDiscount = ((float) ($item->individual_discount_amt ?? 0) != 0.0)
                    ? (float) $item->individual_discount_amt
                    : (float) ($item->discount_amount ?? 0);
                $calculations['discount_total'] += $lineDiscount;
                $calculations['tax_total'] += (float) ($item->tax_amount ?? 0);
            }
        }

        $calculations['discount_total'] += (float) ($booking_info->applied_discount_price ?? 0);
        $payments_display = ['cash' => 0];

        foreach ($booking_info->payments as $row) {
            if ($row->payment_mode === 'cash') {
                $payments_display['cash'] += ($row['amount_taken'] - $row['amount_given']);
            } elseif ($row->payment_mode === 'online') {
                $payments_display['online'] = ($payments_display['online'] ?? 0) + ($row['amount_taken'] - $row['amount_given']);
                $payments_display['card'][] = [
                    'card_type' => $row->card_type,
                    'amount' => $row['amount_taken'] - $row['amount_given'],
                ];
            } elseif ($row->payment_mode === 'credit') {
                $payments_display['credit'] = ($payments_display['credit'] ?? 0) + ($row['amount_taken'] - $row['amount_given']);
                $payments_display['card'][] = [
                    'card_type' => $row->card_type,
                    'amount' => $row['amount_taken'] - $row['amount_given'],
                ];
            } elseif ($row->payment_mode === 'card') {
                $payments_display['card_total'] = ($payments_display['card_total'] ?? 0) + ($row['amount_taken'] - $row['amount_given']);
                $payments_display['card'][] = [
                    'card_type' => $row->card_type,
                    'amount' => $row['amount_taken'] - $row['amount_given'],
                ];
            }
        }

        $client_info = User::find($booking_info->client_id);
        $cashier = User::find($booking_info->created_by);
        $gift_flag = false;
        if ($gift) {
            $gift_flag = true;
        }

        // $vatPercentage = 18;
        $netAmount = $calculations['plain_net_total'] - $calculations['discount_total'];
        $vatAmount = $calculations['tax_total'];
        // $vatAmount = $netAmount * ($vatPercentage / 100);
        $vatPercentage = $netAmount > 0 ? round(($vatAmount / $netAmount) * 100, 2) : 0;

        $totalAmountWithVat = $netAmount + $vatAmount;
        $dateToQr = $booking_info->updated_at;
        $dataToEncode = [
            [1, $seller_name],
            [2, $seller_vat],
            [3, $dateToQr],
            [4, $amountToPay],
            [5, $vatAmount]
        ];

        $amountToPay = $payments_display['online'] ?? 0;

        $business_configs = \App\Models\BusinessConfig::pluck('config_value', 'config_key');
        $upi_id = $business_configs['upi_id'] ?? null;
        $upi_name = $business_configs['upi_name'] ?? null;
        // Config key in DB/company profile is gst_number (legacy key: gst)
        $gst_number = trim((string) ($business_configs['gst_number'] ?? $business_configs['gst'] ?? ''));
        $gst_number = $gst_number !== '' ? $gst_number : null;

        $qrCodeData = 'upi://pay?pa=' . urlencode($upi_id)
            . '&pn=' . urlencode($upi_name)
            . '&am=' . $amountToPay
            . '&cu=INR&tn=' . urlencode('Payment for order');

        // $qrCodeData = 'upi://pay?pa=jimshamol5-1@okicici&pn=jimsha%20mol&am=' . $amountToPay . '&cu=INR&tn=Payment%20for%20order';

        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($qrCodeData);

        $data = [
            // 'qr_code' => $this->getDataForQrCode($qrCodeData),
            'qr_code' => $qrCode,
            'seller_name' => $seller_name,
            // 'seller_name_ar' => $seller_name_ar,
            'seller_vat' => $seller_vat,
            'gst_number' => $gst_number,
            'booking_info' => $booking_info,
            'client_info' => $client_info,
            'payments_display' => $payments_display,
            'calculations' => $calculations,
            'gift_flag' => $gift_flag,
            'cashier' => $cashier,
            'net_amount' => $netAmount,
            'vat_amt' => $vatAmount,
            'total_amount_with_vat' => $totalAmountWithVat,
        ];

        // Get WhatsApp invoice setting from business_configs
        $isSendWhatsappInvoice = DB::table('business_configs')
            ->where('config_key', 'is_send_whatsapp_invoice')
            ->value('config_value');

        // This avoids sending a WhatsApp message every time the invoice page opens.
        if (empty($booking_info->send_whatsapp)) {

            $whatsappResult = $this->sendBookingInvoiceWhatsApp(
                $booking_info,
                $totalAmountWithVat
            );

            // Store 1 for both success and failure
            $booking_info->send_whatsapp = 1;
            $booking_info->save();

            Log::info('Booking WhatsApp invoice processed', [
                'booking_id' => $booking_info->booking_id,
                'success' => $whatsappResult['success'] ?? false,
                'send_whatsapp' => $booking_info->send_whatsapp,
            ]);
        } else {
            Log::info('Booking invoice WhatsApp skipped', [
                'booking_id' => $booking_info->booking_id,
                'already_sent' => $booking_info->send_whatsapp,
            ]);
        }

        if ($request->type == 'web') {
            $pdf = mPDF::loadView('salon.booking-details.invoice', $data);
            return $pdf->download('baheya_booking.pdf');
        } else {
            return view('salon.booking-details.invoice', $data);
        }
    }

    private function sendBookingInvoiceWhatsApp($booking_info, $totalAmountWithVat, ?WhatsAppInvoice $invoiceRecord = null): array
    {

        try {
            $bookingId = $booking_info->booking_id;
            $invoiceNumber = $booking_info->booking_code;
            $invoiceDate = $booking_info->created_at ? $booking_info->created_at->format('d-m-Y') : now()->format('d-m-Y');
            $customerName = $booking_info->client_name;
            $mobile = preg_replace(
                '/\D+/',
                '',
                (string) ($booking_info->client_mobile ?? '')
            );

            if (strlen($mobile) === 10) {
                $mobile = '91' . $mobile;
            }

            if (empty($mobile) || empty($bookingId) || empty($invoiceNumber)) {
                return [
                    'success' => false,
                    'message' => 'Customer mobile, booking ID, or invoice number is missing.',
                ];
            }

            $bodyParams = [
                $customerName,
                $invoiceNumber,
                $invoiceDate,
                number_format($totalAmountWithVat, 2, '.', ''),
            ];

            // Dynamic URL button parameter: booking ID
            $encryptedBookingId = Crypt::encryptString((string) $bookingId);
            Log::info('Encrypted booking ID generated', [
                'booking_id' => $bookingId,
                'encrypted_booking_id' => $encryptedBookingId,
            ]);
            // Generate complete invoice URL
            $invoiceUrl = route('salon.invoice', [
                'id' => $encryptedBookingId,
            ]);
            $buttons = [
                [
                    'type' => 'url',
                    'index' => 0,
                    'value' => $encryptedBookingId,
                ],
            ];

            $result = whatsappGatewayRequest(
                $mobile,
                config('services.whatsapp.template_name'),
                config('services.whatsapp.template_language'),
                null,
                $bodyParams,
                $buttons
            );

            $messageStatus = $result['message_status'] ?? 'F';
            $success = $messageStatus === 'S' || $messageStatus === 'accepted';

            $errorMessage = $result['api_response']['error']['message'] ?? $result['error'] ?? null;

            $invoiceData = [
                'booking_id' => $bookingId,
                'booking_code' => $invoiceNumber,
                'url' => $success ? $invoiceUrl : null,
                'message_status'  => $errorMessage ?? $messageStatus,
                'status' => $success ? 'sent' : 'failed',
            ];

            if ($invoiceRecord) {
                $invoiceRecord->update($invoiceData);
            } else {
                WhatsAppInvoice::create($invoiceData);
            }

            Log::log(
                $success ? 'info' : 'error',
                'WhatsApp invoice sending result',
                [
                    'invoice_id' => $invoiceRecord?->id,
                    'message_status' => $messageStatus,
                    'success'       => $success,
                    'message_id'    => $result['message_id'] ?? null,
                    'error'         => $result['error'] ?? null,
                    'api_response'  => $result['api_response'] ?? null,
                ]
            );
            return [
                'success' => $success,
                'message' => $success
                    ? 'Request Send Sucessfully.'
                    : 'Request Send failed.',
                'result' => $result,
            ];
        } catch (\Throwable $e) {
            Log::error('Booking invoice WhatsApp exception', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Unable to send the invoice through WhatsApp.',
            ];
        }
    }

    // public function viewInvoice($id)
    // {

    //     try {
    //         Log::info('Requested booking ID', [
    //             'booking_id' => $id,
    //         ]);
    //         $bookingId = Crypt::decryptString($id);
    //         Log::info('decrypted booking ID generated', [
    //             'booking_id' => $bookingId,
    //         ]);
    //         // Display the existing invoice page
    //         $bookingRepo = app(BookingRepository::class);

    //         return $this->index(
    //             request(),
    //             $bookingRepo,
    //             $bookingId
    //         );
    //     } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Invalid encrypted booking ID',
    //         ], 400);
    //     }
    // }


    public function viewInvoice($id)
    {
        try {
            // Decode URL-encoded characters.
            $id = rawurldecode($id);

            // Remove the unwanted {{1}} prefix.
            $id = preg_replace('/^\{\{1\}\}/', '', $id);

            // Try decrypting the original token first.
            try {
                $bookingId = Crypt::decryptString($id);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                // Fallback if the token is URL-safe Base64 encoded.
                $base64 = strtr($id, '-_', '+/');

                $base64 .= str_repeat(
                    '=',
                    (4 - strlen($base64) % 4) % 4
                );

                $decoded = base64_decode($base64, true);

                if ($decoded === false) {
                    throw $e;
                }

                $bookingId = Crypt::decryptString($decoded);
            }

            $bookingRepo = app(BookingRepository::class);

            return $this->index(
                request(),
                $bookingRepo,
                $bookingId
            );
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            Log::error('Invoice token decryption failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid encrypted booking ID',
            ], 400);
        }
    }

    public function resendWhatsAppInvoice(
        Request $request,
        $id,
        BookingRepository $booking_repo
    ) {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        try {
            $invoice = WhatsAppInvoice::findOrFail($id);

            if ($invoice->status !== 'failed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only failed invoices can be resent.',
                ], 422);
            }

            $booking_info = $this->findById(
                $invoice->booking_id,
                $booking_repo
            );

            if (!$booking_info) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found.',
                ], 404);
            }

            // Recalculate the invoice total using the original invoice logic.
            $netTotal = 0;
            $taxTotal = 0;
            $discountTotal = 0;

            foreach ($booking_info->bookingOnlyServices as $service) {
                $qty = (float) ($service->quantity ?: 1);

                $netTotal += (float) $service->service_booking_price * $qty;

                $discountTotal +=
                    (float) (($service->individual_discount_amt ?? 0) != 0
                        ? $service->individual_discount_amt
                        : ($service->discount_amount ?? 0));

                $taxTotal += (float) ($service->tax_amount ?? 0);
            }

            foreach ($booking_info->bookingItems as $item) {
                $qty = (float) ($item->quantity ?: 1);

                $netTotal += (float) $item->selling_price * $qty;

                $discountTotal +=
                    (float) (($item->individual_discount_amt ?? 0) != 0
                        ? $item->individual_discount_amt
                        : ($item->discount_amount ?? 0));

                $taxTotal += (float) ($item->tax_amount ?? 0);
            }

            $discountTotal += (float) ($booking_info->applied_discount_price ?? 0);

            $totalAmountWithVat = ($netTotal - $discountTotal) + $taxTotal;

            // Log the resend reason for audit purposes.
            Log::info('WhatsApp invoice resend requested', [
                'invoice_id' => $invoice->id,
                'booking_id' => $invoice->booking_id,
                'reason' => $request->reason,
            ]);

            // Pass the existing failed invoice row to avoid creating a duplicate.
            $result = $this->sendBookingInvoiceWhatsApp(
                $booking_info,
                $totalAmountWithVat,
                $invoice
            );

            if ($result['success'] ?? false) {
                $invoice->update([
                    'reason' => $request->reason,
                    'resend_date' => now(),
                ]);

                $booking_info->send_whatsapp = 1;
                $booking_info->save();

                return response()->json([
                    'success' => true,
                    'message' => 'WhatsApp invoice resent successfully.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'WhatsApp resend failed.',
            ], 422);
        } catch (\Throwable $e) {
            Log::error('WhatsApp invoice resend exception', [
                'invoice_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to resend WhatsApp invoice.',
            ], 500);
        }
    }

    private function findById($id, \App\Repositories\Base\BaseRepository $repo)
    {
        return ($id > 0) ? $repo->model()->findOrFail($id) : $repo->model();
    }


    /*
         * QR Encoding Functions
         */

    private function getLength($value)
    {
        return strlen($value);
    }

    private function toHex($value)
    {
        return pack('H*', sprintf('%02X', $value));
    }

    private function toString($__tag, $__value, $__length)
    {
        $value = (string) $__value;
        return $this->toHex($__tag) . $this->toHex($__length) . $value;
    }

    private function getTLV($dataToEncode)
    {
        $__TLVS = '';
        for ($i = 0; $i < count($dataToEncode); $i++) {
            $__tag = $dataToEncode[$i][0];
            $__value = $dataToEncode[$i][1];
            $__length =  $this->getLength($__value);
            $__TLVS .=  $this->toString($__tag, $__value, $__length);
        }

        return $__TLVS;
    }

    /*
         * QR Encoding Functions
         */

    /*
         * QR Code
         */

    public function getDataForQrCode($upiString)
    {
        // $__TLV = $this->getTLV($dataToEncode);
        // $__QR = base64_encode($__TLV);
        // return   $__QR;
    }


    /*
         * QR Code
         */
}
