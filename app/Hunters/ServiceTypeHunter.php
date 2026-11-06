<?php

namespace App\Hunters;

use App\Hunters\Base\BaseHunter;

class ServiceTypeHunter extends BaseHunter
{
    public function __construct(\App\Models\ServiceTypeManagement $model)
    {
        parent::__construct($model);
    }

    public function applyCriteria()
    {
        return $this->model()
            ->when($this->search_term, function ($query) {
                $query->where('service_type_name', 'like', '%' . $this->search_term . '%');
            });

        /* DO NOT USE get(), first() etc at end of this query builder */
    }

    public function applyCriteria_1()
    {
        return $this->model()
            ->when($this->search_term, function ($query) {
                $query->where('service_type_name', 'like', '%' . $this->search_term . '%');
            });

        /* DO NOT USE get(), first() etc at end of this query builder */
    }
    
}
