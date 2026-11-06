<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class StockMovementRepository extends BaseRepository
{
    public function __construct(\App\Models\StockMovement $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\StockMovementHunter
     */
    public function hunter()
    {
        return new \App\Hunters\StockMovementHunter($this->model());
    }
}
