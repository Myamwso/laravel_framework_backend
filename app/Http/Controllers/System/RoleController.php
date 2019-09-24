<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * 获取全部角色
     * @param Request $request
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function total(Request $request,Role $role)
    {
        $data = $role->select(['id','name'])->get();

        return $this->jsonResult([
            'roles' => $data
        ]);
    }

    /**
     * 获取角色列表
     * @param Request $request
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function listpage(Request $request,Role $role)
    {
        $params = $request->all();
        $data = $role
            ->where('name', 'like', "%{$params['name']}%")
            ->where('name', '!=', 'admin')
            ->paginate(15, ['*'], 'page', $params['page']);

        return $this->jsonResult([
            'total' => $data->total(),
            'roles' => $data->items()
        ]);
    }

    /**
     * 添加角色
     * @param Request $request
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request,Role $role)
    {
        $params = $request->all();
        $result = $role->insert([
            'name' => $params['name'],
            'permission' => json_encode($params['rolePermissions']),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($result) {
            return $this->jsonResult();
        } else {
            return $this->jsonResult([], 10001);
        }
    }

    /**
     * 编辑角色
     * @param Request $request
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request,Role $role)
    {
        $params = $request->all();
        if ($params['id']<0) {
            return $this->jsonResult([], 10002);
        }
        $result = $role->where(['id' => $params['id']])->update([
            'name' => $params['name'],
            'permission' => json_encode($params['rolePermissions']),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($result) {
            return $this->jsonResult();
        } else {
            return $this->jsonResult([], 10001);
        }
    }

    /**
     * 删除角色
     * @param Request $request
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(Request $request,Role $role)
    {
        $params = $request->all();
        if ($params['id']<0) {
            return $this->jsonResult([], 10002);
        }
        $result = $role->where(['id' => $params['id']])->delete();

        if ($result) {
            return $this->jsonResult();
        } else {
            return $this->jsonResult([], 10001);
        }
    }

    /**
     * 批量删除角色
     * @param Request $request
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function batchRemove(Request $request,Role $role)
    {
        $params = $request->all();
        $ids = explode(',', $params['ids']);
        if (empty($ids)) {
            return $this->jsonResult([], 10002);
        }
        $result = $role->whereIn('id', $ids)->delete();

        if ($result) {
            return $this->jsonResult();
        } else {
            return $this->jsonResult([], 10001);
        }
    }
}
