<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class GlobalTaxRepository extends BaseRepository
{
    public function __construct(\App\Models\GlobalTax $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\GlobalTaxHunter
     */
    public function hunter()
    {
        return new \App\Hunters\GlobalTaxHunter($this->model());
    }
}
