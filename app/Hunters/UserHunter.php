<?php

namespace App\Hunters;

use App\Hunters\Base\BaseHunter;

class UserHunter extends BaseHunter
{
    protected $role_name = null;
    protected $user_status = null;

    // SHOULD pass "\App\User" here for permission package
    public function __construct(\App\Models\User $model)
    {
        parent::__construct($model);
    }

    public function setRoleName($roleName)
    {
        $this->role_name = $roleName;
        return $this;
    }

    public function setUserStatus($status)
    {
        $this->user_status = $status;
        return $this;
    }

    public function applyCriteria()
    {
        return $this->model()
            ->with('roles')
            ->when($this->search_term, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search_term . '%')
                        ->orWhere('username', 'like', '%' . $this->search_term . '%');
                });
            })
            ->when($this->role_name, function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', $this->role_name);
                });
            })
            ->when($this->user_status, function ($query) {
                $query->where('user_status', $this->user_status);
            });

            /* @TODO - use `unionAll` for optimization */

            // Do not include clients in search results
            // ->whereNotIn('id', function ($query) {
            //     $query->select('client_id as id')->from('clients');
            // });

            // Do not include beauticians in search results
            // ->whereNotIn('id', function ($query) {
            //     $query->select('beautician_id as id')->from('beauticians');
            // });

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
