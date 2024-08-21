<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{

    public function login(Request $request)
    {
        $fields = $request->validate([
            'username'  => 'required|string',
            'password'  => 'required|string',
        ]);


        $user = User::query()
            ->applyUsername($fields["username"])
            ->applyPassword($fields["password"])
            ->first();



        if (!$user) {
            return response([
                'message' => 'Account doesn\'t match',
            ], 403);
        }

        $delete_previous_token = false;

        if ($delete_previous_token) {
            $user->tokens()->delete();
        }

        return response($user->setAndGetLoginResponse(), 201);
    }

    public function logout(Request $request)
    {

        $user = $request->user('sanctum');

        if ($user instanceof User) {
            $user->tokens()->delete();

            return response()->noContent();
        }

        return response('', 403);
    }
}
