<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
class UserRequest extends FormRequest
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
            'name' => 'required|between:2,25|unique:users,name,' . Auth::id(),
            'email' => 'required|email',
            'introduction' => 'max:80',
            'avatar'=>'max:10240|image'
        ];
    }

    public function messages()
    {
        return [
            'name.require'=>'用户名不能为空',
            'name.unique'=>'用户名已被占用，请重新填写',
            'avatar.max'=>'用户头像最大不能超过10M',
            'avatar.image'=>'用户头像只能为图片',
        ];
    }
}
