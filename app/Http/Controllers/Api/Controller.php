<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
   use Helpers;  //DingoApi 的 helper，这个 trait 可以帮助我们处理接口响应，
}
