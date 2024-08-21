<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;


class RegistrationController extends Controller
{

    public function register(Request $request)
    {

        $request->validate(
            [
                'name' => 'required',
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'required|min:6',
                'email' => 'sometimes|unique:users,email',
            ]
        );

        $user = new User();
        $user->name = $request->get('name', NULL);
        $user->email = $request->get('email', NULL);
        $user->auth_password = $request->get('password', NULL);

        $user->save();

        return response([
            'message' => 'Registration successful!',
            'errors' => null
        ]);
    }
}
