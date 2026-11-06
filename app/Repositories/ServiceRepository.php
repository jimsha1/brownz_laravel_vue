<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class ServiceRepository extends BaseRepository
{
    public function __construct(\App\Models\Service $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\ServiceHunter
     */
    public function hunter()
    {
        return new \App\Hunters\ServiceHunter($this->model());
    }
}
