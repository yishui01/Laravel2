<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ImageRequest;
use App\Transformers\ImageTransformer;
use Illuminate\Http\Request;
use App\Models\Image;
class ImagesController extends Controller
{
    public function store(ImageRequest $imgrequest,  Image $image)
    {
        $user = $this->user();

        //保存图片，返回图片地址即可，用户更新接口那里再记录之前的头像信息到images表
        $path = config('myconfig.file.'.$imgrequest->type);
        $url = $imgrequest->image->store($path);
        $url = '/'.$url;
        $image->path = $url;
        $image->type = $imgrequest->type;
        $image->user_id = $user->id;
        $image->save();
        return $this->response->item($image, new ImageTransformer())->setStatusCode(201);

    }
}
