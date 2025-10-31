<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    public function sendError($message, $errors = [], $code = 400)
    {
        return response()->error($message, $code, $errors);
    }

    public function sendResponse($result, $message, $status = 200)
    {
        return response()->success($result, $message, $status);
    }
}
