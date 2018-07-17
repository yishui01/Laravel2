<?php

namespace App\Http\Controllers\Api;

use function foo\func;
use Illuminate\Http\Request;
use App\Http\Requests\Api\VerificationCodeRequest;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use Illuminate\Support\Facades\Cache;
class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $phone = $request->phone;
        //生成四位随机数
        $code = mt_rand(1000,9999); //验证码
        $expire = config('myconfig.sms.verify_expire'); //持续时间
        $tmplate = config('myconfig.sms.tmpla_verify_code'); //模板内容
        $driver = env('SMS_DRIVER', 'qcloud'); //短信发送的驱动（运营商）默认是腾讯云
        $content = sprintf($tmplate, $code,$expire);

        if(env('APP_ENV') == 'production') {
            try {
                $result = $easySms->send($phone,[
                    'content'=>$content
                ]);
            } catch (NoGatewayAvailableException $e) {
                $message = $e->getException($driver)->getMessage();
                return $this->response->errorInternal($message ?? '短信发送异常');
            }
        }

        $key = 'verificationCode_'.str_random(15);
        $expiredAt = now()->addMinutes($expire);
        // 缓存验证码 10分钟过期。
        Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }

}
