<?php

namespace App\Http\Controllers\Stock\StockVoucherList\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Repositories\TransactionGroupRepository;
use Illuminate\Http\Request;

class TabAllController extends Controller
{
    public function index(Request $request, TransactionGroupRepository $transaction_group_repo, $tab_name)
    {
        // Reset pagination page number, when search form submitted
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        $search = trim((string) $request->input('search', ''));
        $searchByVoucherNumber = $this->isVoucherNumberSearch($search);

        // Default to current month when dates are not provided.
        // A voucher-number search looks up that voucher on any date.
        if (! $searchByVoucherNumber && ! $request->filled('start_date') && ! $request->filled('end_date')) {
            $request->merge([
                'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => now()->toDateString(),
            ]);
        }

        $hunter = $transaction_group_repo->hunter()
            ->setSearchTerm($search !== '' ? $search : null);

        if (! $searchByVoucherNumber) {
            $hunter->setStartDate($request->start_date)
                ->setEndDate($request->end_date);
        }

        $db_records = $hunter
            ->query(function ($query) use ($request) {
                if ($request->filled('voucher_type')) {
                    $query->where('voucher_type', $request->voucher_type);
                }
            })
            ->search();

        $db_records->appends($request->only([
            'search',
            'voucher_type',
            'start_date',
            'end_date',
            'tab_search',
        ]));

        $canEdit = auth()->user()->can('voucher_edit');

        foreach ($db_records as $row) {
            $isBooking = method_exists($row, 'isBookingVoucher') ? $row->isBookingVoucher() : false;

            $row->edit_url = route('tab.stock.stock.voucher.details', ['id' => $row->id, 'tab_name' => 'edit']);
            $row->view_url = route('tab.stock.stock.voucher.details', ['id' => $row->id, 'tab_name' => 'info']);
            $row->is_booking_voucher = $isBooking;
            $row->can_edit = $canEdit && ! $isBooking;
            $row->date_display = $row->date
                ? \Carbon\Carbon::parse($row->date)->format('d M Y')
                : '—';
            $row->description_display = $row->description
                ? \Illuminate\Support\Str::limit($row->description, 70)
                : '—';
            $row->total_amount = round((float) ($row->transactions ?? collect())->sum('debit'), 2);
            $row->created_by_name = optional($row->createdUser)->name ?? '—';
            $row->reference_label = $this->referenceTypeLabel($row->reference_type);
            $row->makeHidden(['transactions', 'createdUser']);
        }

        $db_results = [
            'vue_data' => [
                'vue_list_tab_all' => [
                    'paginator' => paginator_vue_data($db_records),
                ],
            ],
            'response_values' => [
                '#tab_search' => '0',
            ],
        ];

        if ($request->tab_search || $request->pgajax === 'y') {
            return response()->json($db_results);
        }

        return view('stock.stock-voucher-list.ajax-tabs.tab-all', compact('db_results', 'tab_name'));
    }

    /**
     * Voucher numbers look like PVA001 or PVA001-REV. Narration text is left on the date filter.
     */
    private function isVoucherNumberSearch(string $search): bool
    {
        if ($search === '' || preg_match('/\s/', $search)) {
            return false;
        }

        return (bool) preg_match('/^(?:PV|RV|JV|CV|VN)[A-Z]*\d*(?:-REV)?$/i', $search)
            || (bool) preg_match('/^[A-Z]*\d+(?:-REV)?$/i', $search);
    }

    private function referenceTypeLabel(?string $referenceType): ?string
    {
        if (! $referenceType) {
            return null;
        }

        $labels = [
            'commission_payment' => 'Commission',
            'commission_batch_payment' => 'Commission Batch',
            'incentive_payment' => 'Incentive',
            'incentive_batch_payment' => 'Incentive Batch',
            'payroll_payment' => 'Payroll',
            'payroll_batch_payment' => 'Payroll Batch',
            'vendor_payment' => 'Vendor',
        ];

        return $labels[$referenceType] ?? ucwords(str_replace('_', ' ', $referenceType));
    }
}
