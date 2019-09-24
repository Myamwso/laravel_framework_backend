<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Log extends Model
{
    //
    public function getList($params)
    {
//        DB::connection()->enableQueryLog();

        $logJoinUsers = DB::table('logs')
            ->leftJoin('users', 'logs.op_uid', '=', 'users.id');

        $logJoinUsers->select("logs.*","users.user_name","users.id","users.name")
            ->where('users.user_name', 'like', "%{$params['user_name']}%");

        if (! empty($params['select_time'][0]) && ! empty($params['select_time'][1])) {
            $logJoinUsers->where('logs.created_at', ">=", $params['select_time'][0])
                ->where('logs.created_at', "<=", $params['select_time'][1]);
        }

        if (! empty($params['permission']) && $params['permission'] != 'undefined') {
            $permission = str_replace("_", "\/", $params['permission']);
            $logJoinUsers->where('logs.request', "like", "%{$permission}%");
        }

        $list=$logJoinUsers->orderBy("logs.created_at", "desc")
            ->paginate(15, ['*'], 'page', $params['page']);

        $data = [];
        if (! empty($list->items())) {
            foreach ($list->items() as $k=>$val) {
                $val->request = unserialize($val->request);
                $val->response = unserialize($val->response);
                $data[] = $val;
            }
        }

//        print_r(DB::getQueryLog());
        return [$list, $data];
    }
}
