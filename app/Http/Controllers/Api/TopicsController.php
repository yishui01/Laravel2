<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\TopicRequest;
use Illuminate\Http\Request;
use App\Models\Topic;
use App\Transformers\TopicTransformer;
use Dingo\Api\Auth\Auth;
use App\Models\User;
class TopicsController extends Controller
{
    //获取话题列表
    public function index(Request $request)
    {
        $order = $request->order;
        $request = $request->only(['user_id', 'category_id','created_at', 'updated_at','excerpt','order']);
        $where = [];
        foreach ($request as $key=>$item) {
            switch ($key) {
                case 'created_at':
                    $where[] = [$key, '>=', $item];
                case 'updated_at':
                    $where[] = [$key, '<=', $item];
                default:
                    $where[] = [$key, '=', $item];
            }
        }
        $pageSize = config('myconfig.page.api.pageSize');
        $topics = Topic::where($where)->withOrder($order)->paginate($pageSize);
        return $this->response->paginator($topics, new TopicTransformer());

    }
    //获取用户发表的所有话题
    public function userIndex(User $user)
    {
        $topics = $user->topics()->recent()->paginate(20);
        return $this->response->paginator($topics, new TopicTransformer());
    }

    //话题详情接口
    public function show(Topic $topic)
    {
        return $this->response->item($topic, new TopicTransformer());
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
