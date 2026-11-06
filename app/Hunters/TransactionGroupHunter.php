<?php

namespace App\Hunters;

use App\Hunters\Base\BaseHunter;

class TransactionGroupHunter extends BaseHunter
{
    public function __construct(\App\Models\TransactionGroup $model)
    {
        parent::__construct($model);
    }

    public function applyCriteria()
    {
        return $this->model()
            ->with(['transactions', 'createdUser'])
            ->where('is_create_voucher', 1)
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
                $query->where(function ($where) {
                    $where->where('voucher_no', 'like', '%'.$this->search_term.'%')
                        ->orWhere('voucher_type', 'like', '%'.$this->search_term.'%')
                        ->orWhere('description', 'like', '%'.$this->search_term.'%');
                });
            })
            ->orderByDesc('date')
            ->orderByDesc('id');

        /* DO NOT USE get(), first() etc at end of this query builder */
    }

    public function applyCriteria_1()
    {
        return $this->applyCriteria();
    }
}
