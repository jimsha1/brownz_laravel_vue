<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;
use App\Models\ServiceTypeManagement;

class ServiceTypeRepository extends BaseRepository
{
    public function __construct(\App\Models\ServiceTypeManagement $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\ServiceHunter
     */
    public function hunter()
    {
        return new \App\Hunters\ServiceTypeHunter($this->model());
    }
}
