<?php

namespace App\Http\Controllers\Stock\StockVoucherDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StockVoucherEditLog;
use App\Repositories\TransactionGroupRepository;
use Auth;

class TabInfoController extends Controller
{
    public function index(Request $request, TransactionGroupRepository $transaction_group_repo, $id, $tab_name)
    {
        $stock_voucher_info = $transaction_group_repo->model()
                ->with('transactions.journal.account')
                ->with('createdUser')
                ->with('updatedUser')
                ->with(['editLogs.editedUser'])
                ->findOrFail($id);

        if ($stock_voucher_info->editLogs->isEmpty() && $stock_voucher_info->voucher_no) {
            $stock_voucher_info->setRelation(
                'editLogs',
                StockVoucherEditLog::with('editedUser')
                    ->where('voucher_no', $stock_voucher_info->voucher_no)
                    ->orderByDesc('created_at')
                    ->get()
            );
        }

        $details_info_vue = [
            'id' => $id,
            'tab_name' => $tab_name,
            'stock_voucher_info' => $stock_voucher_info,
        ];

        return view('stock.stock-voucher-details.ajax-tabs.tab-info', compact('details_info_vue'));
    }
}
