<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class BeauticianServiceRepository extends BaseRepository
{
    public function __construct(\App\Models\BeauticianService $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\BeauticianServiceHunter
     */
    public function hunter()
    {
        return new \App\Hunters\BeauticianServiceHunter($this->model());
    }
}
