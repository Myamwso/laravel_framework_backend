<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class AppToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('X-Token');
        list($auth, $time, $userId) = explode("|", $token);

        $mineToken = Redis::get(sprintf('linsd_system:token:%s', $userId));
        if (empty($mineToken) || $mineToken != $token) {
            return response()->json([
                'code' => 20000,
                'message' => 'token_error'
            ]);
        }

        $xPermission = ltrim(str_replace("/", "_",$request->getpathinfo()), "_");
        $admin = Redis::hgetall(sprintf("linsd_admin_%s", $userId));
        $permission = json_decode( $admin['permission'] , true);
        $role = json_decode( $admin['role'] , true);

        if (! in_array('admin',$role)) {
            if (empty($xPermission) || ! in_array($xPermission, $permission)) {
                return response()->json([
                    'code' => 20001,
                    'message' => '对不起，您无该操作权限'
                ]);
            }
        }

        return $next($request);
    }
}
