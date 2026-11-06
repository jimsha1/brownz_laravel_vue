<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class VendorRepository extends BaseRepository
{
    public function __construct(\App\Models\Vendor $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\VendorHunter
     */
    public function hunter()
    {
        return new \App\Hunters\VendorHunter($this->model());
    }
}
