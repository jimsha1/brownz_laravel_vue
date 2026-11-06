<?php

namespace App\Hunters;

use App\Hunters\Base\BaseHunter;
use App\Models\Beautician;

class BeauticianHunter extends BaseHunter
{
    protected $status = null;

    public function __construct(\App\Models\Beautician $model)
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
            ->join('users', 'beauticians.beautician_id', '=', 'users.id')
            ->join('employees', 'employees.user_id', '=', 'beauticians.beautician_id')
            ->where('beauticians.created_outlet_id', session_outlet_id())
            ->when($this->status === 'active', function ($query) {
                $query->where('beauticians.status', Beautician::STATUS_ACTIVE)
                    ->where('employees.status', 'active')
                    ->where('users.user_status', 'active');
            })
            ->when($this->status === 'inactive', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('beauticians.status', Beautician::STATUS_INACTIVE)
                        ->orWhere('employees.status', 'inactive')
                        ->orWhere('users.user_status', 'inactive');
                });
            })
            ->when($this->search_term, function ($query) {
                $term = '%'.$this->search_term.'%';
                $query->where(function ($q) use ($term) {
                    $q->where('users.name', 'like', $term)
                        ->orWhere('beauticians.beautician_code', 'like', $term)
                        ->orWhere('users.mobile', 'like', $term);
                });
            })
            ->select(
                'beauticians.beautician_id',
                'beauticians.beautician_code',
                'beauticians.status as beautician_status',
                'beauticians.join_date',
                'beauticians.created_outlet_id',
                'users.name',
                'users.mobile',
                'users.gender',
                'users.profile_photo_path',
                'users.user_status',
                'employees.status as employee_status'
            )
            ->orderBy('users.name', 'asc');

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
