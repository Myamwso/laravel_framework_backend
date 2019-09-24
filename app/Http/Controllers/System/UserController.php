<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    /**
     * 获取用户信息
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(Request $request,User $user)
    {
        if ($this->userId <= 0) {
            $token = $request->header('X-Token');
            return $this->jsonResult([$token], 10002);
        }

        $userInfo = $user->getInfo($this->userId);

        return $this->jsonResult([
            'name' => $userInfo['user']->name,
            'roles' => $userInfo['roles'],
            'permissions' => $userInfo['permissions']
        ]);
    }

    /**
     * 登录
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request, User $user)
    {
        $password = $this->_encodePwd(trim($request['password']));
        $where = [
            'user_name' => $request['user_name'],
            'password' => $password
        ];
        $userInfo = $user->where($where)->first();

        if (! isset($userInfo['id']) || $userInfo['id'] < 0) { //用户名或密码错误
            $user->where(['user_name' => $request['user_name']])->increment('error_amount');
            return $this->jsonResult([], 10003);
        }

        $user->where(['user_name' => $request['user_name']])->update([
            'error_amount' => 0,
            'last_ip' =>$request->getClientIp(),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        //发放校验令牌
        $time = time();
        $auth = md5(md5(sprintf("%s_%s_%s", $time, "34jkjf234KGDF3ORGI4j", $userInfo['id'])));
        $token = sprintf("%s|%s|%s",$auth, $time, $userInfo['id']);
        Redis::set(sprintf('linsd_system:token:%s', $userInfo['id']), $token);
        Redis::expire(sprintf('linsd_system:token:%s', $userInfo['id']), 86400);

        return $this->jsonResult([
            'token' => $token
        ]);
    }

    /**
     * 退出登录
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $result = Redis::del(sprintf('linsd_system:token:%s', $this->userId));
        if ($result) {
            Redis::del(sprintf("permission_%s", $this->userId));
            return $this->jsonResult();
        } else {
            return $this->jsonResult([], 10001);
        }
    }

    /**
     * 获取用户列表
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function listpage(Request $request,User $user)
    {
        $params = $request->all();
        $data = $user->where('name', 'like', "%{$params['name']}%")->paginate(15, ['*'], 'page', $params['page']);

        return $this->jsonResult([
            'total' => $data->total(),
            'users' => $data->items()
        ]);
    }

    /**
     * 添加用户
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request,User $user)
    {
        $params = $request->all();
        $result = $user->insert([
            'name' => $params['name'],
            'user_name' => $params['user_name'],
            'password' => $this->_encodePwd(trim($params['password'])),
            'status' => 1,
            'avatar' => '',
            'roles' => json_encode($params['user_roles']),
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
     * 编辑用户
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request,User $user)
    {
        $params = $request->all();
        if ($params['id']<0) {
            return $this->jsonResult([], 10002);
        }
        $data = [
            'name' => $params['name'],
            'user_name' => $params['user_name'],
            'status' => (int)$params['status'],
            'roles' => json_encode($params['user_roles']),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        if (!empty($params['password']) && $params['password'] == $params['re_password']) {
            $data['password'] = $this->_encodePwd(trim($params['password']));
        }

        $result = $user->where(['id' => $params['id']])->update($data);

        if ($result) {
            return $this->jsonResult();
        } else {
            return $this->jsonResult([], 10001);
        }
    }

    /**
     * 删除用户
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(Request $request,User $user)
    {
        $params = $request->all();
        if ($params['id']<0) {
            return $this->jsonResult([], 10002);
        }
        $result = $user->where(['id' => $params['id']])->delete();

        if ($result) {
            return $this->jsonResult();
        } else {
            return $this->jsonResult([], 10001);
        }
    }

    /**
     * 批量删除用户
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function batchRemove(Request $request,User $user)
    {
        $params = $request->all();
        $ids = explode(',', $params['ids']);
        if (empty($ids)) {
            return $this->jsonResult([], 10002);
        }
        $result = $user->whereIn('id', $ids)->delete();

        if ($result) {
            return $this->jsonResult();
        } else {
            return $this->jsonResult([], 10001);
        }
    }

    /**
     * 获取加密密码
     * @param $pwd
     * @return string
     */
    private function _encodePwd($pwd)
    {
        return md5(base64_encode(sprintf("%s%s%s", $pwd, '1sdfiuKJO9ihIbiuu02F31S', $pwd)));
    }
}
