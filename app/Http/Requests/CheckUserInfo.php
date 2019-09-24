<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckUserInfo extends FormRequest
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
            'nick_name' => 'required',
            'description' => 'required',
            'cate_mine' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'nick_name.required' => 10101,
            'description.required'  => 10102,
            'cate_mine.required'  => 10103,
        ];
    }

}
