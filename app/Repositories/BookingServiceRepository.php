<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class BookingServiceRepository extends BaseRepository
{
    public function __construct(\App\Models\BookingService $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\BookingServiceHunter
     */
    public function hunter()
    {
        return new \App\Hunters\BookingServiceHunter($this->model());
    }
}
