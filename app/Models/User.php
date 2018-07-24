<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasRoles;
    use Traits\ActiveUserHelper;
    use Traits\LastActivedAtHelper;//RecordLastActivedTime前置中间件中记录用户登录时间到redis，需要调用方法在这个trait中

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','introduction','avatar','phone',
        'weixin_openid', 'weixin_unionid','registration_id',
        'weixin_session_key', 'weapp_openid',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    use Notifiable{
        notify as protected laravelNotify;
    }

    //这是一个修改器，方法名必须按照格式来定义set{属性的驼峰式命名}Attribute（访问器是 访问属性时 修改，修改器是在 写入数据库前 修改）
    public function setPasswordAttribute($value)
    {
        // 如果值的长度等于 60，即认为是已经做过加密的情况
        if (strlen($value) != 60) {

            // 不等于 60，做密码加密处理
            $value = bcrypt($value);
        }

        $this->attributes['password'] = $value;
    }

    public function setAvatarAttribute($path)
    {
        // 如果不含uploads，需要补全 URL
        if ( ! strpos ($path, 'uploads') && strpos($path, 'http') === FALSE) {
            // 拼接完整的 URL
            $path = "/uploads/images/avatars/".$path;
        }
        $this->attributes['avatar'] = $path;
    }


    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    //封装权限验证
    public function isAuthorOf($model)
    {
        return $this->id == $model->user_id;
    }

    public function notify($instance)
    {
        // 如果要通知的人是当前用户，就不必通知了！
        if ($this->id == Auth::id()) {
            return;
        }
        $this->increment('notification_count');
        $this->laravelNotify($instance);
    }

    //将所有通知状态设定为已读，并清空未读消息数。
    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }

    // Rest omitted for brevity

    public function getJWTIdentifier()
    {
        return $this->getKey(); //返回了用户表的主键也就是id
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
