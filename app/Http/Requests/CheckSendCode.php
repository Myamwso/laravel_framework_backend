<?php

namespace App\Http\Requests;

use App\Rules\Captcha;
use App\Rules\Tel;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CheckSendCode extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required',
            'vcode' => ['required',new Captcha()],
            'phone' => ['required', new Tel()],
        ];
    }

    public function messages()
    {
        return [
            'type.required' => 10012,
            'vcode.required'  => 10005,
            'phone.required'  => 10003
        ];
    }

    /**
     * 配置验证器实例 - 后置验证。
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = new User();
            $userInfo = $user->where(['user_name' => $_POST['phone']])->first();
            if (! isset($userInfo['id']) || $userInfo['id'] < 0) {
                $validator->errors()->add('phone', "10008");
            }

            $time = Redis::ttl(sprintf('gzh_task_sys_manage:codeTtl:%s', $_POST['phone']));
            if ($time > 0) {
                $validator->errors()->add('phone', "10006|{$time}");
            }
        });
    }
}
