<?php

namespace App\Hunters;

use App\Hunters\Base\BaseHunter;

class StockOpenBalanceHunter extends BaseHunter
{
    public function __construct(\App\Models\StockOpenBalance $model)
    {
        parent::__construct($model);
    }

    public function applyCriteria()
    {
        return $this->model()
            ->with(['account'])
            ->when($this->start_date || $this->end_date, function ($query) {
                if ($this->start_date && $this->end_date) {
                    $query->whereBetween('opening_date', [$this->start_date, $this->end_date]);
                } elseif ($this->start_date) {
                    $query->whereDate('opening_date', '>=', $this->start_date);
                } elseif ($this->end_date) {
                    $query->whereDate('opening_date', '<=', $this->end_date);
                }
            })
            ->when($this->search_term, function ($query) {
                $query->where(function ($where) {
                    $where->orWhereHas('account', function ($q) {
                        $q->where('name', 'like', '%' . $this->search_term . '%');
                    });
                });
            })
            ->orderByDesc('opening_date')
            ->orderByDesc('open_balance_id');

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
    public function findPurchase($open_balance_id)
    {
        $open_balance_info = $this->applyPagination(false)
            ->search_1()
            ->where('open_balance_id', $open_balance_id)->with('account')
            ->firstOrFail();

        return $open_balance_info;
    }
}
