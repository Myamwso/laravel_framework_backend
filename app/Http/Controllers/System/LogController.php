<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * 获取列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listpage(Request $request,Log $log)
    {
        $params = $request->all();
        list($list, $data) = $log->getList($params);

        return $this->jsonResult([
            'total' => $list->total(),
            'logs' => $data
        ]);
    }
}
