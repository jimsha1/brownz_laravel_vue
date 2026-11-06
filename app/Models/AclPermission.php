<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class AclPermission extends Permission
{
    use \VocomiApp\Http\Traits\ConstantExporter;

    const PERMISSION_GROUP_OUTLET = 'outlet';
    const PERMISSION_GROUP_AUTHORIZATION = 'authorization';
    const PERMISSION_GROUP_GLOBAL_SETTINGS = 'global_settings';
    const PERMISSION_GROUP_POS_BOOKING = 'pos_booking';
    const PERMISSION_GROUP_APPOINTMENT = 'appointment';
    const PERMISSION_GROUP_ADMINISTRATION = 'administration';

    public function getPermissionGroups()
    {
        return $this->exportConstants('PERMISSION_GROUP_');
    }
}
