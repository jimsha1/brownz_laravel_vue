<?php

namespace App\Hunters;

use App\Hunters\Base\BaseHunter;

class ServiceCategoryHunter extends BaseHunter
{
    protected $status = null;
    protected $show_on_website = null;
    protected $parent_id = null;

    public function __construct(\App\Models\ServiceCategory $model)
    {
        parent::__construct($model);
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function setShowOnWebsite($showOnWebsite)
    {
        $this->show_on_website = $showOnWebsite;
        return $this;
    }

    public function setParentId($parentId)
    {
        $this->parent_id = $parentId;
        return $this;
    }

    public function applyCriteria()
    {
        return $this->model()
            ->when($this->search_term, function ($query) {
                $term = '%' . $this->search_term . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                        ->orWhere('tagline_en', 'like', $term)
                        ->orWhere('description_en', 'like', $term);
                });
            })
            ->when($this->status === 'active', function ($query) {
                $query->where('is_active', 1);
            })
            ->when($this->status === 'inactive', function ($query) {
                $query->where('is_active', 0);
            })
            ->when($this->show_on_website === 'yes', function ($query) {
                $query->where('show_on_website', 1);
            })
            ->when($this->show_on_website === 'no', function ($query) {
                $query->where('show_on_website', 0);
            })
            ->when($this->parent_id === 'root', function ($query) {
                $query->whereNull('parent_id');
            })
            ->when($this->parent_id && $this->parent_id !== 'root', function ($query) {
                $query->where('parent_id', $this->parent_id);
            })
            ->when($this->parent_id === null, function ($query) {
                // Parent first, then its children grouped underneath
                $query->orderByRaw('COALESCE(parent_id, category_id) ASC')
                    ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END ASC')
                    ->orderBy('display_order')
                    ->orderBy('name');
            }, function ($query) {
                $query->orderBy('display_order')
                    ->orderBy('name');
            });

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
