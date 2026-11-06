<?php

namespace App\Hunters;

use App\Hunters\Base\BaseHunter;

class VendorHunter extends BaseHunter
{
    protected $status = null;
    protected $credit_balance = null;

    public function __construct(\App\Models\Vendor $model)
    {
        parent::__construct($model);
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function setCreditBalance($creditBalance)
    {
        $this->credit_balance = $creditBalance;
        return $this;
    }

    public function applyCriteria()
    {
        return $this->model()
            ->when($this->search_term, function ($query) {
                $term = '%'.$this->search_term.'%';
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                        ->orWhere('phone', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('address', 'like', $term)
                        ->orWhere('gst_number', 'like', $term);
                });
            })
            ->when($this->status === 'active', function ($query) {
                $query->where('is_active', 1);
            })
            ->when($this->status === 'inactive', function ($query) {
                $query->where(function ($q) {
                    $q->where('is_active', 0)->orWhereNull('is_active');
                });
            })
            ->when($this->credit_balance === 'yes', function ($query) {
                $query->where('balance', '>', 0);
            })
            ->orderBy('name', 'asc');

        /* DO NOT USE get(), first() etc at end of this query builder */
    }

    public function applyCriteria_1()
    {
        return $this->model()
            ->when($this->search_term, function ($query) {
                $query->where('brand_name', 'like', '%'.$this->search_term.'%');
            });

        /* DO NOT USE get(), first() etc at end of this query builder */
    }
}
