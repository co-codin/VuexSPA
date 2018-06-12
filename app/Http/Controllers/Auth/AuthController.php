<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\{JWTException};
use App\Http\Requests\Auth\{RegisterFormRequest, LoginFormRequest};


class AuthController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
      $this->auth = $auth;
    }

    public function login(LoginFormRequest $request)
    {
      try {
        if (!$token = $this->auth->attempt($request->only('email', 'password'))) {
          return response()->json([
                    'errors' => [
                        'root' => 'Could not sign you in with those details.'
                    ]
                ], 401);
        }
      } catch (JWTException $e) {
        return response()->json([
          'errors' => [
              'root' => 'Failed.'
          ]
        ], $e->getStatusCode());
      }

      return response()->json([
            'data' => $request->user(),
            'meta' => [
                'token' => $token
            ]
      ], 200);
    }

    public function register(RegisterFormRequest $request)
    {
      $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password)
      ]);

      $token = $this->auth->attempt($request->only(['email', 'password']));

      return response()->json([
        'data' => $user,
        'meta' => [
          'token' => $token
        ]
      ], 200);
    }
}
