<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class PermissionGroup extends Model
{
    public static function getPermissionsWithGroup()
    {
        $groups = self::get();
        foreach ($groups as $group) {
            $group->permissions = Permission::where(['p_group' => $group->id])->get();
        }
        return $groups;
    }
}
