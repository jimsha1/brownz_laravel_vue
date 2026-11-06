<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class ShiftRepository extends BaseRepository
{
    public function __construct(\App\Models\Shift $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\ShiftHunter
     */
    public function hunter()
    {
        return new \App\Hunters\ShiftHunter($this->model());
    }
}
