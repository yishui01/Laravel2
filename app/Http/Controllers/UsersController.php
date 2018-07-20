<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Handlers\ImageUploadHandler;
class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);
    }

    public function show(User $user)
    {
       return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $userRquest,  User $user)
    {
        $this->authorize('update', $user);
        $info = $userRquest->all();
        if($userRquest->avatar) {
            //上传了新文件
            $path = config('myconfig.file.avatar');
            $url = $info['avatar']->store($path);
            $url = '/'.$url;
            $info['avatar'] = $url;
        }
        $user->update($info);
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }
}
