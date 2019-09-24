<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Redis;

class MsgCode implements Rule
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
        if ($_SERVER['APP_DEBUG'] == true) { //debug模式不校验
            return true;
        }

        $phone = empty($_POST['phone']) ? $_POST['user_name']: $_POST['phone'];
        $code = Redis::get(sprintf('gzh_task_sys_manage:code:%s', $phone));

        if ($code == $value) {
            Redis::del(sprintf('gzh_task_sys_manage:code:%s', $phone));
            Redis::del(sprintf('gzh_task_sys_manage:codeTtl:%s', $phone));
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 10002;
    }
}
