<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class UserHasOutletRepository extends BaseRepository
{
    public function __construct(\App\Models\UserHasOutlet $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\UserHasOutletHunter
     */
    public function hunter()
    {
        return new \App\Hunters\UserHasOutletHunter($this->model());
    }
}
