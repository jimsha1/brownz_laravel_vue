<?php

namespace VocomiApp\DbHunter\Base;

abstract class BaseHunter implements \VocomiApp\DbHunter\DbHunterInterface\HunterInterface
{
    const SORT_BY_RELEVANT = 'sort_by_relevant';
    const SORT_BY_CREATED = 'sort_by_created';
    const SORT_BY_UPDATED = 'sort_by_updated';
    const SORT_BY_PRIMARY = 'sort_by_primary';

    protected $query_where_params = [];
    protected $query_where_in_params = [];

    public function __construct()
    {
        //
    }

    protected function addWhere($where)
    {
        $this->query_where_params[] = $where;
    }

    protected function hasWhere()
    {
        return count($this->query_where_params) > 0;
    }

    protected function addWhereIn($where_in)
    {
        $this->query_where_in_params[] = $where_in;
    }

    protected function hasWhereIn()
    {
        return count($this->query_where_in_params) > 0;
    }

    public function sortRelevant()
    {
        //
    }

    final protected function applyWhere($query)
    {
        // where conditions
        $query->when($this->hasWhere(), function ($query) {
                    return $query->where($this->query_where_params);
                })

                // whereIn conditions
                ->when($this->hasWhereIn(), function ($query) {
                    foreach ($this->query_where_in_params as $condition) {
                        $query->whereIn($condition[0], $condition[1]);
                    }

                    return $query;
                });

        return $query;
    }

}
