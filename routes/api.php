<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::get('/skt', 'TopicsController@index')->name('root');


$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => ['serializer:array','bindings'], //这个中间件可以将DataArraySerializer转换成ArraySerializer，少一层嵌套结构包裹
], function($api) {

    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.sign.limit'),
        'expires' => config('api.rate_limits.sign.expires'),
    ], function($api) {
        /*************游客可以访问的接口*************/
        // 短信验证码
        $api->post('verificationCodes', 'VerificationCodesController@store')
            ->name('api.verificationCodes.store');
        // 用户注册
        $api->post('users', 'UsersController@store')
            ->name('api.users.store');
        // 图片验证码
        $api->post('captchas', 'CaptchasController@store')
            ->name('api.captchas.store');
        // 第三方登录
        $api->post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
            ->name('api.socials.authorizations.store');
        // 登录
        $api->post('authorizations', 'AuthorizationsController@store')
            ->name('api.authorizations.store');
        // 刷新token
        $api->put('authorizations/current', 'AuthorizationsController@update')
            ->name('api.authorizations.update');
        // 删除token
        $api->delete('authorizations/current', 'AuthorizationsController@destroy')
            ->name('api.authorizations.destroy');
        //获取分类列表
        $api->get('categories', 'CategoriesController@index')
            ->name('api.categories.index');
        // 获取话题列表
        $api->get('topics', 'TopicsController@index')
            ->name('api.topics.index');
        //查看用户发表的所有的话题
        $api->get('users/{user}/topics', 'TopicsController@userIndex')
            ->name('api.users.topics.index');
        //话题详情
        $api->get('topics/{topic}', 'TopicsController@show')
            ->name('api.topics.show');



        /***********需要 token 验证的接口***************/
        //使用token换取用户信息
        $api->group(['middleware' => 'api.auth'], function($api) {
            // 获取登录用户信息
            $api->get('user', 'UsersController@me')
                ->name('api.user.show');
            // 编辑登录用户信息
            $api->patch('user', 'UsersController@update')
                ->name('api.user.update');
            // 上传图片
            $api->post('images', 'ImagesController@store')
                ->name('api.images.store');
            // 发布话题
            $api->post('topics', 'TopicsController@store')
                ->name('api.topics.store');
            // 修改话题
            $api->patch('topics/{topic}', 'TopicsController@update')
                ->name('api.topics.update');
            //删除话题
            $api->delete('topics/{topic}', 'TopicsController@destroy')
                ->name('api.topics.destroy');
            // 发布回复
            $api->post('topics/{topic}/replies', 'RepliesController@store')
                ->name('api.topics.replies.store');
        });


    });

});


$api->version('v2', function($api) {
    $api->get('version', function() {
        return response('this is version v2');
    });
});
