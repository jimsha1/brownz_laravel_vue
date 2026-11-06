<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class StockItemPriceRepository extends BaseRepository
{
    public function __construct(\App\Models\StockItemPrice $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\StockItemPriceHunter
     */
    public function hunter()
    {
        return new \App\Hunters\StockItemPriceHunter($this->model());
    }
}
