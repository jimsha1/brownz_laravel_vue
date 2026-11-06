<?php

namespace App\Hunters;

use App\Hunters\Base\BaseHunter;

class ServiceHunter extends BaseHunter
{
    protected $status = null;
    protected $show_on_website = null;
    protected $category_id = null;

    public function __construct(\App\Models\Service $model)
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

    public function setCategoryId($categoryId)
    {
        $this->category_id = $categoryId;
        return $this;
    }

    public function applyCriteria()
    {
        return $this->model()
            ->with('category.parent')
            ->when($this->search_term, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search_term . '%')
                        ->orWhereHas('category', function ($categoryQuery) {
                            $categoryQuery->where('name', 'like', '%' . $this->search_term . '%');
                        })
                        ->orWhereHas('category.parent', function ($parentQuery) {
                            $parentQuery->where('name', 'like', '%' . $this->search_term . '%');
                        });
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
            ->when($this->category_id, function ($query) {
                $categoryId = $this->category_id;
                $query->where(function ($q) use ($categoryId) {
                    $q->where('parent_category_id', $categoryId)
                        ->orWhere('category_id', $categoryId);
                });
            })
            ->when($this->category_id === null, function ($query) {
                // Parent category, then subcategory, then service order
                $query->orderBy('parent_category_id')
                    ->orderBy('category_id')
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
            ->with('category')
            ->when($this->search_term, function ($query) {
                $query->where('name', 'like', '%' . $this->search_term . '%');
            })
            ->orderBy('service_id', 'desc');

        /* DO NOT USE get(), first() etc at end of this query builder */
    }
}
