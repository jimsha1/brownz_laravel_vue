<?php

namespace App\Repositories\Base;

abstract class BaseRepository implements \App\Repositories\Interfaces\RepositoryInterface
{
    /**
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model = null;

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

    public function createFromRequest(\Illuminate\Http\Request $request)
    {
        $this->model->create($request->all());

        return $this->model;
    }

    public function findBy($attribute, $value, $columns = ['*'])
    {
        return $this->model->where($attribute, $value)->first($columns);
    }

    public function getBy($attribute, $value, $columns = ['*'])
    {
        return $this->model->where($attribute, $value)->get($columns);
    }
    
}
