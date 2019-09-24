<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class Captcha implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $deviceId = $_SERVER['HTTP_DEVICEID'];
        $vcode = Redis::get(sprintf('gzh_task_sys_manage:vcode:%s', $deviceId));
//        Redis::del(sprintf('gzh_task_sys_manage:vcode:%s', $deviceId));
        return $vcode == $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 10005;
    }
}
