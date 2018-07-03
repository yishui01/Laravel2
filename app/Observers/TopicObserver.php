<?php

namespace App\Observers;

use App\Models\Topic;

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
    }
}