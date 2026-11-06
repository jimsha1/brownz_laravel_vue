<?php

namespace App\Http\Controllers\Vendors\VendorList\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Repositories\VendorRepository;
use Illuminate\Http\Request;

class TabAllController extends Controller
{
    public function index(Request $request, VendorRepository $vendor_repo, $tab_name)
    {
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        $status = $request->status ?: 'active';
        $creditBalance = $request->credit_balance ?: '';

        $hunter = $vendor_repo->hunter()
            ->setSearchTerm($request->search)
            ->setStatus($status === 'all' ? null : $status)
            ->setCreditBalance($creditBalance);

        $db_records = $hunter->search();

        $allMatching = $vendor_repo->hunter()
            ->setSearchTerm($request->search)
            ->setStatus($status === 'all' ? null : $status)
            ->setCreditBalance($creditBalance)
            ->applyPagination(false)
            ->search()
            ->get();

        $totalCredit = (float) $allMatching->where('balance', '>', 0)->sum('balance');
        $withCreditCount = $allMatching->where('balance', '>', 0)->count();

        foreach ($db_records as $row) {
            $row->edit_url = route('tab.vendors.vendor.details', [
                'vendor_id' => $row->vendor_id,
                'tab_name' => 'edit',
            ]);
            $row->view_url = route('tab.vendors.vendor.details', [
                'vendor_id' => $row->vendor_id,
                'tab_name' => 'info',
            ]);
            $row->payment_url = route('tab.vendors.vendor.details', [
                'vendor_id' => $row->vendor_id,
                'tab_name' => 'payment',
            ]);
            $row->voucher_pay_url = route('tab.stock.stock.voucher.details', [
                'id' => 0,
                'tab_name' => 'edit',
            ]).'?vendor_id='.$row->vendor_id;
            $row->has_credit = (float) ($row->balance ?? 0) > 0;
            $row->status_label = ((int) ($row->is_active ?? 0) === 1) ? 'active' : 'inactive';
            $row->address_short = $row->address
                ? \Illuminate\Support\Str::limit(trim($row->address), 48)
                : null;
        }

        $db_results = [
            'vue_data' => [
                'vue_list_tab_all' => [
                    'paginator' => paginator_vue_data($db_records),
                    'filters' => [
                        'search' => (string) ($request->search ?? ''),
                        'status' => $status,
                        'credit_balance' => $creditBalance,
                    ],
                    'summary' => [
                        'total' => $allMatching->count(),
                        'total_credit' => $totalCredit,
                        'with_credit_count' => $withCreditCount,
                    ],
                ],
            ],
            'response_values' => [
                '#tab_search' => '0',
            ],
        ];

        if ($request->tab_search || $request->pgajax === 'y') {
            return response()->json($db_results);
        }

        return view('vendors.vendor-list.ajax-tabs.tab-all', compact('db_results', 'tab_name'));
    }
}
