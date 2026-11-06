<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class BookingPaymentRepository extends BaseRepository
{
    public function __construct(\App\Models\BookingPayment $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\BookingPaymentHunter
     */
    public function hunter()
    {
        return new \App\Hunters\BookingPaymentHunter($this->model());
    }
}
