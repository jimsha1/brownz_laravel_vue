<?php

namespace App\Repositories;

use App\Repositories\Base\BaseRepository;

class AclRoleRepository extends BaseRepository
{
    public function __construct(\App\Models\AclRole $model)
    {
        parent::__construct($model);
    }

    /**
     *
     * @return \App\Hunters\AclRoleHunter
     */
    public function hunter()
    {
        return new \App\Hunters\AclRoleHunter($this->model());
    }
}
