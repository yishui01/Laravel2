<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\TopicRequest;
use Dingo\Api\Contract\Http\Request;
use App\Models\Topic;
use App\Transformers\TopicTransformer;
use Dingo\Api\Auth\Auth;
use App\Models\User;
class TopicsController extends Controller
{
    //获取话题列表
    public function index(Request $request, Topic $topic)
    {
        $topics = $topic->withOrder($request->order)->paginate(20);
        return $this->response->paginator($topics, new TopicTransformer());

    }
    //获取用户发表的所有话题
    public function userIndex(User $user)
    {
        $topics = $user->topics()->recent()->paginate(20);
        return $this->response->paginator($topics, new TopicTransformer());
    }

    //发表话题接口
    public function store(TopicRequest $request, Topic $topic)
    {
        $topic->fill($request->all());
        $topic->user_id = $this->user()->id;
        $topic->save();
        return $this->response->item($topic, new TopicTransformer())->setStatusCode(201);
    }

    //修改话题
    public function update(TopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);
        $topic->update($request->all());
        return $this->response->item($topic, new TopicTransformer());
    }

    //删除话题
    public function destroy(Topic $topic, TopicRequest $request)
    {
        $this->authorize('destroy', $topic);
        $topic->delete();
        return $this->response->noContent();
    }



}
