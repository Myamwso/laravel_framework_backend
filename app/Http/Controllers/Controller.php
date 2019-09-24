<?php

namespace App\Http\Controllers;

use App\Log;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

    public $userId = 0;

    protected $logId = 0;

    public function __construct(Request $request)
    {
        $log = new Log();
        $token = $request->header('X-Token');
        if (! empty($token) ) {
            list($auth, $time, $this->userId) = explode("|",$token);
        }

        $opPath = explode("/", $request->getpathinfo());
        $op = end($opPath);
        if ($this->userId > 0 && in_array($op, ['add', 'edit', 'remove', 'batchRemove'])) {
            $requestMsg = serialize([
                'url' => $request->getpathinfo(),
                'param' => $request->all()
            ]);

            $this->logId = $log->insertGetId([
                'op_uid' => $this->userId,
                'ip' => $request->getClientIp(),
                'request' => $requestMsg,
                'response' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    private $_error = [
        10001 => '操作失败，请联系管理员',
        10002 => '参数错误',
        10003 => '用户名或密码错误',
    ];

    protected $user = [];

    protected function jsonResult($data=[], $code=0)
    {
        $log = new Log();
        if ($code != 0) {
            $result = [
                'code' => $code,
                'message' => $this->_error[$code],
                'data' => $data
            ];
            $httpCode = 201;
        } else {
            $result = array_merge([
                'code' => 0,
                'message' => 'success',
            ],$data);
            $httpCode = 200;
        }

        if ($this->logId > 0) {
            $log->where(['id' => $this->logId])->update([
                'response' => serialize($result),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return response()->json($result, $httpCode);
    }
}
