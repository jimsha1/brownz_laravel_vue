<?php

namespace App\Hunters\Base;

abstract class BaseHunter
{
    /**
     *
     * @var string Search term
     */
    protected $search_term = null;
    protected $start_date = null;
    protected $end_date = null;

    /**
     *
     * @var boolean paginate results
     */
    protected $paginate = true;

    /**
     *
     * @var boolean append 'pgajax' parameter to pagination results
     */
    protected $paginate_pgajax = true;

    /**
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model = null;

    /**
     * @var array List of callback functions
     */
    private $callbacks = [];

    /**
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct(\Illuminate\Database\Eloquent\Model $model)
    {
        $this->model = $model;
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function model()
    {
        return $this->model;
    }

    public function setSearchTerm($search)
    {
        $this->search_term = $search;
        return $this;
    }
    public function setStartDate($start){
        $this->start_date = $start;
        return $this;
    }
    public function setEndDate($end){
        $this->end_date = $end;
        return $this;
    }

    public function applyPagination($apply = true, $append_pgajax = true)
    {
        $this->paginate = (bool) $apply;
        $this->paginate_pgajax = (bool) $append_pgajax;

        return $this;
    }

    final public function search()
    {
        $results = $this->applyCriteria()
            ->when($this->callbacks, function($query) {
                foreach($this->callbacks as $cb) {
                    $cb($query);
                }
            });

        if ($this->paginate) {
            if ($this->paginate_pgajax) {
                return $results->paginate(config('vocomi.settings.pagination.perpage.admin'))
                                ->appends(['pgajax' => 'y']);
            }

            return $results->paginate(config('vocomi.settings.pagination.perpage.admin'));
        }

        return $results;
    }




    public function query(\Closure $callback)
    {
        $this->callbacks[] = $callback;
        return $this;
    }

    final public function search_1()
    {
        $results = $this->applyCriteria_1()
            ->when($this->callbacks, function($query) {
                foreach($this->callbacks as $cb) {
                    $cb($query);
                }
            });

        if ($this->paginate) {
            if ($this->paginate_pgajax) {
                return $results->paginate(config('vocomi.settings.pagination.perpage.admin'))
                                ->appends(['pgajax' => 'y']);
            }

            return $results->paginate(config('vocomi.settings.pagination.perpage.admin'));
        }

        return $results;
    }
    abstract protected function applyCriteria_1();
    abstract protected function applyCriteria();
}
