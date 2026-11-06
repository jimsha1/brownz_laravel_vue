<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class AccountRepository extends BaseRepository
{
    public function __construct(\App\Models\Account $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\AccountHunter
     */
    public function hunter()
    {
        return new \App\Hunters\AccountHunter($this->model());
    }
}
