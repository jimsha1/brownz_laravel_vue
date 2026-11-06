<?php

namespace App\Hunters;

use App\Hunters\Base\BaseHunter;

class ShiftHunter extends BaseHunter
{
    protected $status = null;

    public function __construct(\App\Models\Shift $model)
    {
        parent::__construct($model);
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function applyCriteria()
    {
        return $this->model()
            ->when($this->search_term, function ($query) {
                $query->where('title', 'like', '%' . $this->search_term . '%');
            })
            ->when($this->status === 'active', function ($query) {
                $query->where('is_active', 1);
            })
            ->when($this->status === 'inactive', function ($query) {
                $query->where('is_active', 0);
            })
            ->orderBy('shift_id', 'desc');

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
}
