<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Api\UserRequest;

class UsersController extends Controller
{
    public function store(UserRequest $request)
    {

        $verifyData = Cache::get($request->verification_key);

        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        if (!hash_equals((string)$verifyData['code'], $request->verification_code)) {
            // 返回401
            return $this->response->errorUnauthorized('验证码错误');
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => bcrypt($request->password),
        ]);

        // 清除验证码缓存
        //Cache::forget($request->verification_key);

        /*return $this->response->array([
            'errcode'=>0,
            'msg' => '创建成功',
            'data' =>''
        ])->setStatusCode(201);*/
        return $this->response
            ->item($user, new UserTransformer()) //设置刚刚创建的用户数据
            ->setMeta([ //设置meta头结构
                'access_token' => \Auth::guard('api')->fromUser($user),
                'token_type' => 'Bearer',
                'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
            ])
            ->setStatusCode(201);

    }

    public function me()
    {
        return $this->response->item($this->user(), new UserTransformer());
    }
}
