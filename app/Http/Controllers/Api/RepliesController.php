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

    

}
