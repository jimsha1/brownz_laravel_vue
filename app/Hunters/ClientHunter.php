<?php

namespace App\Hunters;

use App\Hunters\Base\BaseHunter;

class ClientHunter extends BaseHunter
{
    protected $status = null;
    protected $credit_balance = null;

    public function __construct(\App\Models\Client $model)
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
            ->join('users', 'clients.client_id', '=', 'users.id')
            ->select(
                'clients.client_id',
                'clients.balance',
                'clients.client_type',
                'users.name',
                'users.mobile',
                'users.email',
                'users.address',
                'users.gender',
                'users.user_status'
            )
            ->when($this->search_term, function ($query) {
                $term = '%'.$this->search_term.'%';
                $query->where(function ($q) use ($term) {
                    $q->where('users.name', 'like', $term)
                        ->orWhere('users.mobile', 'like', $term)
                        ->orWhere('users.email', 'like', $term);
                });
            })
            ->when($this->status && $this->status !== 'all', function ($query) {
                $query->where('users.user_status', $this->status);
            })
            ->when($this->credit_balance === 'yes', function ($query) {
                $query->where('clients.balance', '>', 0);
            })
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
