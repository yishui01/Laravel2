<?php

return [

    //文件上传配置
    'file'=>[
        'avatars'=>'/uploads'.'/'.date("Ymd", time()).'/avatars/',
        'topic'=>'/uploads'.'/'.date("Ymd", time()).'/topic/',
    ],

    //错误日志位置
    'error_log'=>[
        'sms'=>storage_path('logs').'/'.'sms.errlog', //短信发送错误日志
    ],

    //短信发送配置
    'sms'=>[
        'verify_dirver'=>env('SMS_DRIVER', 'qcloud'), //验证码发送的运营商
        'verify_expire'=>30,//minutes
        'tmpla_verify_code'=>'您的验证码为%u，请于%s分钟内填写。如非本人操作，请忽略本短信',
    ]


];