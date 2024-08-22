<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthUserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {

        $auth = $request->user('sanctum');

        $is_logged_in = (bool) $auth;

        $user = $is_logged_in ? AuthUserResource::make($auth) : null;

        return response([
            'user' => $user,
            'is_logged_in' => $is_logged_in
        ]);
    }
}
