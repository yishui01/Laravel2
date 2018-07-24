<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\Api\WeappAuthorizationRequest;
class AuthorizationsController extends Controller
{
    //这方法是用户通过第三方登录的时候获取用户信息并注册用户
    public function socialStore($type, SocialAuthorizationRequest $request)
    {
        if (!in_array($type, ['weixin'])) {
            return $this->response->errorBadRequest();
        }

        $drive = Socialite::driver($type);

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
            return $this->response->errorUnauthorized(trans('auth.failed'));
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
                        'weixin_unionid'=>$unionid,
                    ]);
                }

                break;

        }
        $apitoken = Auth::guard('api')->fromUser($user);
        return $this->respondWithToken($apitoken)->setStatusCode(201);
    }

    //直接通过邮箱或手机号+密码登录
    public function store(AuthorizationRequest $request)
    {
        $username = $request->username;

        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username
            :
            $credentials['phone'] = $username
        ;
        $credentials['password'] = $request->password;

        if (!$apitoken = Auth::guard('api')->attempt($credentials)) {
           return $this->response->errorUnauthorized('用户名或密码错误');
        }

        return $this->respondWithToken($apitoken)->setStatusCode(201);

    }

    //小程序登录
    public function weappStore(WeappAuthorizationRequest $request)
    {
        $code = $request->code;

        //根据code获取微信的openid和session_key
        $mini = \EasyWeChat::miniProgram();
        $data = $mini->auth->session($code);

        //如果结果错误，说明code已经过期或者不正确，返回401
        if (isset($data['errcode'])) {
            return $this->response->errorUnauthorized('code不正确');
        }

        //根据openid查找是否有对应的用户
        $user = User::where('weapp_openid', $data['openid'])->first();
        $attributes['weixin_session_key'] = $data['session_key'];

        //未找到对于的用户则需要提交用户名密码将小程序的openid与用户进行绑定
        if (!$user) {
            //如果未提交用户名密码，返回403
            if (!$request->username) {
               return $this->response->errorForbidden('用户不存在');
            }

            $username = $request->username;

            //用户名可以是邮箱也可以是电话
            filter_var($username, FILTER_VALIDATE_EMAIL) ?
                $credentials['email'] = $username
                :
                $credentials['phone'] = $username;
            $credentials['password'] = $request->password;

            //验证用户名密码是否正确
            if (!Auth::guard('api')->once($credentials)) {
                return $this->response->errorUnauthorized('用户名或密码错误');
            }

            //获取对应用户
            $user = Auth::guard('api')->getUser();
            $attributes['weapp_openid'] = $data['openid'];
        }

        //更新用户数据
        $user->update($attributes);

        //为用户创建jwt
        $token = Auth::guard('api')->fromUser($user);

        return $this->respondWithToken($token)->setStatusCode(201);

    }

    //刷新token
    public function update()
    {
        $token = Auth::guard('api')->refresh();
        return $this->respondWithToken($token);
    }

    //删除token
    public function destroy()
    {
        Auth::guard('api')->logout();
        return $this->response->noContent();
    }

    protected function respondWithToken($apitoken)
    {
        return $this->response->array([
            'access_token' => $apitoken,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
}
