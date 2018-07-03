<?php

namespace App\Observers;

use App\Models\Topic;
use App\Handlers\SlugTranslateHandler;
use App\Jobs\TranslateSlug;
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
    //添加/更新完之后触发
    public function saved(Topic $topic)
    {
        // 使用翻译器对 title 进行翻译
        //if ( ! $topic->slug) { //原本是slug字段无内容的时候再触发，更新时不会触发，现在去掉这个条件
        //现在每次添加/更新的时候都会重新生成seo后缀
            dispatch(new TranslateSlug($topic)); //放入队列中，而不是下面的同步执行
            //$topic->slug = app(SlugTranslateHandler::class)->translate($topic->title);
        //}
    }
}