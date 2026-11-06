<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class StockOpenBalanceRepository extends BaseRepository
{
    public function __construct(\App\Models\StockOpenBalance $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\StockOpenBalanceHunter
     */
    public function hunter()
    {
        return new \App\Hunters\StockOpenBalanceHunter($this->model());
    }
}
