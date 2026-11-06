<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class TransactionGroupRepository extends BaseRepository
{
    public function __construct(\App\Models\TransactionGroup $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\TransactionGroupHunter
     */
    public function hunter()
    {
        return new \App\Hunters\TransactionGroupHunter($this->model());
    }
}
