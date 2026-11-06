<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class StockPurchaseRepository extends BaseRepository
{
    public function __construct(\App\Models\StockPurchase $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\StockPurchaseHunter
     */
    public function hunter()
    {
        return new \App\Hunters\StockPurchaseHunter($this->model());
    }
}
