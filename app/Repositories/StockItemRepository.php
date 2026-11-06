<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class StockItemRepository extends BaseRepository
{
    public function __construct(\App\Models\StockItem $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\StockItemHunter
     */
    public function hunter()
    {
        return new \App\Hunters\StockItemHunter($this->model());
    }
}
