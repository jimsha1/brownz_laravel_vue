<?php

namespace App\Hunters;

use App\Hunters\Base\BaseHunter;

class ExpenseHunter extends BaseHunter
{
    public function __construct(\App\Models\Expense $model)
    {
        parent::__construct($model);
    }

    public function applyCriteria()
    {
        return $this->model()
            ->with('account')
            // Filter by date range when provided
            ->when($this->start_date || $this->end_date, function ($query) {
                if ($this->start_date && $this->end_date) {
                    $query->whereBetween('date', [$this->start_date, $this->end_date]);
                } elseif ($this->start_date) {
                    $query->whereDate('date', '>=', $this->start_date);
                } elseif ($this->end_date) {
                    $query->whereDate('date', '<=', $this->end_date);
                }
            })
            ->when($this->search_term, function ($query) {
            $query->whereHas('account', function ($q) {
                $q->where('name', 'like', '%' . $this->search_term . '%');
            });
        });
        /* DO NOT USE get(), first() etc at end of this query builder */
    }
    public function applyCriteria_1()
    {
        return $this->model()
            ->with('account')
            ->when($this->search_term, function ($query) {
                $query->where('name', 'like', '%' . $this->search_term . '%');
            });

        /* DO NOT USE get(), first() etc at end of this query builder */
    }
}
