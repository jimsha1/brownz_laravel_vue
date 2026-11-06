<?php

namespace App\Repositories\Interfaces;

interface RepositoryInterface
{
    public function model();
//    public function create($data);
    public function createFromRequest(\Illuminate\Http\Request $request);
    public function findBy($attribute, $value, $columns = array('*'));
    public function getBy($attribute, $value, $columns = array('*'));
//    public function all($columns = array('*'));
//    public function paginate($per_page = 15, $columns = array('*'));
//    public function update(array $data, $id);
//    public function delete($id);
//    public function find($id, $columns = array('*'));
}
