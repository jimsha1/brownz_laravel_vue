<?php

namespace App\Hunters;

use App\Hunters\Base\BaseHunter;

class StockPurchaseHunter extends BaseHunter
{
    public function __construct(\App\Models\StockPurchase $model)
    {
        parent::__construct($model);
    }

    public function applyCriteria()
    {
        return $this->model()
            ->with(['vendor', 'product_rows', 'payment_rows'])
            ->orderBy('purchase_id', 'desc')
            ->when($this->search_term, function ($query) {
                $query->where(function ($where) {
                    $where->where('purchase_invoice_no', 'like', '%' . $this->search_term . '%')
                        ->orWhere('purchase_ref_no', 'like', '%' . $this->search_term . '%')
                        ->orWhere('purchase_date', 'like', '%' . $this->search_term . '%')
                        ->orWhereHas('vendor', function ($q) {
                            $q->where('name', 'like', '%' . $this->search_term . '%');
                        });
                });
            });

        /* DO NOT USE get(), first() etc at end of this query builder */
    }
    public function applyCriteria_1()
    {
        return $this->model()
            ->when($this->search_term, function ($query) {
                $query->where('brand_name', 'like', '%' . $this->search_term . '%');
            });

        /* DO NOT USE get(), first() etc at end of this query builder */
    }

    // @TODO - don't allow user to access bookings of other outlets
    public function findPurchase($purchase_id)
    {
        $purchase_info = $this->applyPagination(false)
            ->search_1()
            ->where('purchase_id', $purchase_id)->with('product_rows', 'payment_rows', 'vendor')
            ->firstOrFail();

        return $purchase_info;
    }
}
