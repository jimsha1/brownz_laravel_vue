<?php

namespace App\Hunters;

use App\Hunters\Base\BaseHunter;

class StockItemHunter extends BaseHunter
{
    public function __construct(\App\Models\StockItem $model)
    {
        parent::__construct($model);
    }

    public function applyCriteria()
    {
        return $this->model()
            ->with(['category.parent', 'brand', 'stockUom'])
            ->when($this->search_term, function ($query) {
                $query->where(function ($where) {
                    $where->where('item_code', 'like', '%' . $this->search_term . '%')
                        ->orWhere('item_name', 'like', '%' . $this->search_term . '%')
                        ->orWhere('description', 'like', '%' . $this->search_term . '%');
                });
            })
            ->orderBy('item_id', 'desc');

        /* DO NOT USE get(), first() etc at end of this query builder */
    }
    public function applyCriteria_1()
    {
        return $this->model()
            ->when($this->search_term, function ($query) {
                $query->where('brand_name', 'like', '%' . $this->search_term . '%');
            });

        /* DO NOT USE get(), first() etc at end of this query builder */
    }
}
