<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class OutletRepository extends BaseRepository
{
    public function __construct(\App\Models\Outlet $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\OutletHunter
     */
    public function hunter()
    {
        return new \App\Hunters\OutletHunter($this->model());
    }
}
