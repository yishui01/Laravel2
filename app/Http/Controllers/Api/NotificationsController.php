<?php

namespace App\Http\Controllers\Api;

use App\Transformers\NotificationTransformer;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    //查询某个用户的当前通知
    public function index()
    {
        $notifications = $this->user->notifications()->paginate(20);
        return $this->response->paginator($notifications, new NotificationTransformer());
    }

    //当前未读通知总数
    public function stats()
    {
        return $this->response->array([
            'unread_count' => $this->user()->notification_count,
        ]);
    }

    //查看未读通知（清空未读计数）
    public function read()
    {
        $this->user()->markAsRead();
        return $this->response->noContent();
    }

}
