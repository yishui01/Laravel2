<?php

namespace App\Observers;

use App\Models\Topic;
use App\Handlers\SlugTranslateHandler;
// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    public function creating(Topic $topic)
    {

    }

    public function updating(Topic $topic)
    {
        //
    }

    //每次更新之前，调用save方法时触发
    public function saving(Topic $topic)
    {
        $topic->body = clean($topic->body, 'user_topic_body'); //使用HTMLPurifier对内容自动进行过滤
        $topic->excerpt = make_excerpt($topic->body);
        // 如 slug 字段无内容，即使用翻译器对 title 进行翻译
        if ( ! $topic->slug) {
            $topic->slug = app(SlugTranslateHandler::class)->translate($topic->title);
        }
    }
}