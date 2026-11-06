<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class AclRole extends Role
{
    const ROLE_SUPER_ADMIN = 'super_admin';
}
