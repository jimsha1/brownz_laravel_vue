<?php

function db_prefixed($name)
{
    return \DB::getTablePrefix() . $name;
}

function capture_fk_parent_fail(\Illuminate\Database\QueryException $e)
{
    if ($e->errorInfo[1] == 1451) {
        return 'The record is already in use and can\'t be deleted';
    }

    return null;
}

function user_relation_columns(...$relations)
{
    $user_columns = ['name', 'username', 'email', 'profile_photo_path', 'user_status'];

    $combined_columns = [];
    foreach($relations as $rel) {
        $combined_columns = array_merge($combined_columns, array_map(function($value) use($rel) {
            return $rel . '.' . $value . ' as '. $rel . '_' . $value;
        }, $user_columns));
    }

    return $combined_columns;
}
