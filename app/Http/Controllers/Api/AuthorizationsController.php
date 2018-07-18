<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\SocialAuthorizationRequest;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class AuthorizationsController extends Controller
{
    public function socialStore($type, SocialAuthorizationRequest $request)
    {
        //这方法是用户通过第三方登录的时候获取用户信息并注册用户
        if (!in_array($type, ['weixin'])) {
            return $this->response->errorBadRequest();
        }

        $drive = Socialite::driver('weixin');

        //客户端可以直接传code，让服务端请求accesstoken和openid再请求userinfo
        //客户端也可以自己拿code去请求，然后返回服务端accesstoken和openid
        try {
            if ($code = $request->code) {
                //如果传过来的是code
                $resopnse = $drive->getAccessTokenResponse($code);
                $token = array_get($resopnse, 'access_token');
            } else {
                //如果直接传的token
                $token = $request->access_token;

                if($type == 'weixin') {
                    $drive->setOpenId($request->openid);
                }
            }

            $oauth_user = $drive->userFromToken($token);
        } catch (\Exception $e) {
            return $this->response->errorUnauthorized('参数错误，未获取用户信息');
        }

        switch ($type) {
            case 'weixin':
                $unionid = $oauth_user->offsetExists('unionid') ? $oauth_user->offsetGet('unionid') : null;

                if ($unionid) {
                    $user = User::where('weixin_unionid', $unionid)->first();
                } else {
                    $user = User::where('weixin_openid', $oauth_user->getId())->first();
                }

                //如果没有用户，就创建一个
                if(!$user) {
                    $user = User::create([
                        'name'=>$oauth_user->getNickname(),
                        'avatar'=>$oauth_user->getAvatar(),
                        'weixin_openid'=>$oauth_user->getId(),
                        'weixin_unioncode'=>$unionid,
                    ]);
                }

                break;

        }

        return $this->response->array(['token' => $user->id]);
    }
}
