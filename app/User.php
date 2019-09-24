<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class User extends Model
{
    //
    public function getInfo($id)
    {
        $user = $this->where(['id'=>$id])->first();
        $roleIds = json_decode($user['roles'], true);
        if (! empty($roleIds)) {
            $rolesList = DB::table("roles")->whereIn('id', $roleIds)->get();
        }

        $roles = [];
        $permissionsTmp = [];
        if (! empty($rolesList)) {
            foreach ($rolesList as $role) {
                $roles[] = $role->name;
                $permission = json_decode($role->permission, true);
                $permissionsTmp = array_keys(@array_flip($permissionsTmp)+@array_flip($permission));
            }
        }
        $permissions = [];
        if ($permissionsTmp) {
            foreach ($permissionsTmp as $permission) {
                $permissionArr = explode("_", $permission);
                $count = count($permissionArr);
                $permissions[$permission] = $permission;

                if ($count > 1)  {
                    for ($i=0;$i<$count;$i++) {
                        if ($i==0) {
                            $permissions[$permissionArr[$i]] = $permissionArr[$i];
                        } else {
                            $index = $permissionArr[$i-1] ."_". $permissionArr[$i];
                            $permissions[$index] = $index;
                        }
                    }
                }
            }
        }
        sort($permissions);

        Redis::hmset(sprintf("linsd_admin_%s", $id), [
            'role' => json_encode($roles),
            'permission' => json_encode($permissions)
        ]);
        ///
        return [
            'user' => $user,
            'roles' => $roles,
            'permissions' => $permissions
        ];
    }
}
