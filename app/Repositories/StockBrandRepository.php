<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class StockBrandRepository extends BaseRepository
{
    public function __construct(\App\Models\StockBrand $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\StockBrandHunter
     */
    public function hunter()
    {
        return new \App\Hunters\StockBrandHunter($this->model());
    }
}
