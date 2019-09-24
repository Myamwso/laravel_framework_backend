<?php

namespace App\Http\Requests;

use App\Rules\MsgCode;
use App\Rules\Tel;
use Illuminate\Foundation\Http\FormRequest;

class CheckRegister extends FormRequest
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
            'user_name' => ['required', new Tel()],
            'password' => 'required|alpha_dash|min:8|max:15',
            'code' => ['required', new MsgCode()],
        ];
    }

    public function messages()
    {
        return [
            'user_name.required' => 10003,
            'password.required'  => 10004,
            'password.min'  => 10004,
            'password.max'  => 10004,
            'password.alpha_dash'  => 10004,
            'code.required'  => 10002,
        ];
    }
}
