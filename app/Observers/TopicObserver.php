<?php

namespace App\Observers;

use App\Models\Topic;
use App\Handlers\SlugTranslateHandler;
use App\Jobs\TranslateSlug;
// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{

    //每次更新之前，调用save方法时触发
    public function saving(Topic $topic)
    {
        $topic->body = clean($topic->body, 'user_topic_body'); //使用HTMLPurifier对内容自动进行过滤
        $topic->excerpt = make_excerpt($topic->body);
    }

    //创建或者更新时将翻译任务推送到队列
    public function saved(Topic $topic)
    {
        // 推送任务到队列
        dispatch(new TranslateSlug($topic));
        //$topic->slug = app(SlugTranslateHandler::class)->translate($topic->title);
    }

    //话题删除时删除评论
    public function deleted(Topic $topic)
    {
        \DB::table('replies')->where('topic_id', $topic->id)->delete();
    }


}