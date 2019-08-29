<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use App\Models\User;

class AuthController extends Controller
{
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
        $this->user = $this->jwt->user();
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'username'    => 'required|max:190',
            // 'email'    => 'required|email|max:190',
            'password' => 'required|min:6|max:30',
        ]);

        try {

            $user = User::where('username', $request->username)->first();
            // $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Username tidak terdaftar'
                ], 404);
            }

            if ($user->status != 'active') {
                return response()->json([
                    'message' => 'Pegguna tidak aktif, harap hubungi Administrator'
                ], 404);
            }

            if (! $token = $this->jwt->attempt($request->only('username', 'password'))) {
                return response()->json(['Username atau Password yang anda masukkan salah'], 404);
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['Token expired, harap login ulang'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent' => $e->getMessage()], 500);

        }

        // return response()->json(compact('token'));
        return response()->json([
            'user' => $this->jwt->user(),
            'token' => $token
        ]);
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255|min:2',
            'username' => 'required|unique:users|max:255|min:2',
            'password' => 'required|confirmed|min:6|max:30',
            'email' => 'nullable|email|unique:users|max:190',
            'phone' => 'nullable|unique:users|min:6|max:15',
            // 'status' => 'in:active,non_active,need_activation',
        ]);

        $input = $request->except(['password_confirmation']);
        $input['name'] = ucwords($request->name);
        $input['status'] = 'active';
        $input['role'] = 'surveyor';
        $input['password'] = app('hash')->make($input['password']);

        try {
            $user = User::create($input);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json([
            'message' => 'Data Pegguna berhasil terdaftar.'
        ], 201);

    }

    public function logout(Request $request)
    {
        $this->jwt->parseToken()->invalidate();

        return response()->json([
            'message' => 'Token removed.'
        ], 200);
    }

    public function me()
    {
        return response()->json([
            'user' => $this->jwt->user()
        ], 200);
    }

}
