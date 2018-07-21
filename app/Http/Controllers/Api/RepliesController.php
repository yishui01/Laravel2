<?php

namespace App\Http\Controllers\Api;

use App\Models\Reply;
use App\Models\Topic;
use App\Http\Requests\ReplyRequest;
use App\Transformers\ReplyTransformer;
class RepliesController extends Controller
{
    //发表评论接口
    public function store(Topic $topic, Reply $reply, ReplyRequest $Replyrequest)
    {
        //获取请求参数中的数据，向评论表添加数据即可
        $reply->content =$Replyrequest->content;
        $reply->topic_id =$topic->id;
        $reply->user_id =\Auth::id();
        $reply->save();
        return $this->response->item($reply, new ReplyTransformer())->setStatusCode(201);
    }

    //删除回复
    public function destroy(Topic $topic, Reply $reply)
    {
        if ($reply->topic_id != $topic->id) {
            //我感觉这个判断是多余的，反正评论你有权限就删咯，跟topic有什么关系，传A的topic删B的reply删了也无所谓啊
            return $this->response->errorBadRequest();
        }
        $this->authorize('destroy', $reply);
        $reply->delete();
        return $this->response->noContent();
    }

}
