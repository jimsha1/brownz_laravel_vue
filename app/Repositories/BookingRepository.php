<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class BookingRepository extends BaseRepository
{
    public function __construct(\App\Models\Booking $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\BookingHunter
     */
    public function hunter()
    {
        return new \App\Hunters\BookingHunter($this->model());
    }
}
