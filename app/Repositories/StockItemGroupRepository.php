<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class StockItemGroupRepository extends BaseRepository
{
    public function __construct(\App\Models\StockItemGroup $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\StockItemGroupHunter
     */
    public function hunter()
    {
        return new \App\Hunters\StockItemGroupHunter($this->model());
    }
}
