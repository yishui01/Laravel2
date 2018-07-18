<?php

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

function make_excerpt($value, $length = 200)
{
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($value)));
    return str_limit($excerpt, $length);
}

function model_admin_link($title, $model)
{
    return model_link($title, $model, 'admin');
}

function model_link($title, $model, $prefix = '')
{
    // 获取数据模型的复数蛇形命名
    $model_name = model_plural_name($model);

    // 初始化前缀
    $prefix = $prefix ? "/$prefix/" : '/';

    // 使用站点 URL 拼接全量 URL
    $url = config('app.url') . $prefix . $model_name . '/' . $model->id;

    // 拼接 HTML A 标签，并返回
    return '<a href="' . $url . '" target="_blank">' . $title . '</a>';
}

function model_plural_name($model)
{
    // 从实体中获取完整类名，例如：App\Models\User
    $full_class_name = get_class($model);

    // 获取基础类名，例如：传参 `App\Models\User` 会得到 `User`
    $class_name = class_basename($full_class_name);

    // 蛇形命名，例如：传参 `User`  会得到 `user`, `FooBar` 会得到 `foo_bar`
    $snake_case_name = snake_case($class_name);

    // 获取子串的复数形式，例如：传参 `user` 会得到 `users`
    return str_plural($snake_case_name);
}

//随机返回图片地址
function get_rand_imgurl()
{
    $avatars = [
        'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1531882917857&di=9e1197b3ab56544e1d4c56e6523a96bb&imgtype=0&src=http%3A%2F%2Fimg5.duitang.com%2Fuploads%2Fitem%2F201610%2F14%2F20161014213607_5ciFR.jpeg',
        'https://lccdn.phphub.org/uploads/avatars/1_1530614766.png?imageView2/1/w/200/h/200',
        'https://lccdn.phphub.org/uploads/avatars/17626_1499958413.JPG?imageView2/1/w/200/h/200',
        'https://lccdn.phphub.org/uploads/avatars/5350_1481857380.jpg?imageView2/1/w/100/h/100',
        'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1531883452426&di=881f70c78b93bd91906bb224fe12b067&imgtype=0&src=http%3A%2F%2Fpic.hanhande.com%2Ffiles%2F140917%2F1285740_102303_5420.gif',
        'https://lccdn.phphub.org/uploads/avatars/20188_1511849977.JPG?imageView2/1/w/200/h/200',
        'https://lccdn.phphub.org/uploads/avatars/20269_1512030996.jpeg?imageView2/1/w/200/h/200',
        'https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=1468890659,201072083&fm=27&gp=0.jpg',
        'https://lccdn.phphub.org/uploads/avatars/19867_1515925556.png?imageView2/1/w/200/h/200'
    ];

    return $avatars[rand(0, count($avatars)-1)];
}