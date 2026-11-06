<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class UserLoginHistoryRepository extends BaseRepository
{
    public function __construct(\App\Models\UserLoginHistory $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\UserLoginHistoryHunter
     */
    public function hunter()
    {
        return new \App\Hunters\UserLoginHistoryHunter($this->model());
    }
}
