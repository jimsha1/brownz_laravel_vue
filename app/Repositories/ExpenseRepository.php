<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class ExpenseRepository extends BaseRepository
{
    public function __construct(\App\Models\Expense $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\ExpenseHunter
     */
    public function hunter()
    {
        return new \App\Hunters\ExpenseHunter($this->model());
    }
}
