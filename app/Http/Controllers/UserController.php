<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use App\Models\User;

class UserController extends Controller
{
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
        $this->user = $this->jwt->user();
    }

    public function index(Request $request)
    {
        if ($this->user->role != 'admin'){
            return response()->json([
                'message' => 'Anda tidak berhak untuk mengakses fungsi ini!'
            ], 400);
        }

        $list_user = User::latest();
        $perPage = $request->has('per_page') ? $perPage = $request->per_page : $perPage = 'all';
        $list_user = $perPage == 'all' ? $list_user->get() : $list_user->paginate((int)$perPage);

        if ($list_user) {
            return $list_user;
        }
        return response()->json([
            'message' => 'Data Pengguna tidak ditemukan!'
        ], 404);
    }

    public function store(Request $request)
    {
        if ($this->user->role != 'admin'){
            return response()->json([
                'message' => 'Anda tidak berhak untuk mengakses fungsi ini!'
            ], 400);
        }

        $this->validate($request, [
            'name' => 'required|max:255|min:2',
            'email' => 'required|email|unique:users|max:190',
            'password' => 'required|confirmed|min:6|max:30',
            'phone' => 'nullable|unique:users|max:15',
            'status' => 'in:active,non_active,need_activation',
            'role' => 'required|in:pegawai,accounting,kepala_gudang,salesman,driver,logistik,pimpinan,admin'
        ]);

        $input = $request->except(['password_confirmation']);
        $input['status'] = 'active';
        $input['password'] = app('hash')->make($input['password']);
        $input = ['created_by' => $this->user->id];

        try {
            $user = User::create($input);

            // if($user->role == 'salesman') {
            //     $data_sales['user_id'] = $user->id;
            //     $sales = Salesman::create($data_sales);
            // }
            // elseif($user->role == 'kepala_gudang') {
            //     $data_kepala_gudang['user_id'] = $user->id;
            //     $kepala_gudang = KepalaGudang::create($data_kepala_gudang);
            // }
            // elseif($user->role == 'driver') {
            //     $data_driver['user_id'] = $user->id;
            //     $driver = Driver::create($data_driver);
            // }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json([
            'message' => 'Data Pegguna berhasil disimpan.'
        ], 201);
    }

    public function show($id)
    {
        if ($this->user->role != 'admin'){
            return response()->json([
                'message' => 'Anda tidak berhak untuk mengakses fungsi ini!'
            ], 400);
        }

        $user = User::find($id);

        if ($user) {
            return $user;
        }
        return response()->json([
            'message' => 'Data Pengguna tidak ditemukan!'
        ], 404);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $this->validate($request, [
            'name' => 'required|max:255|min:2',
            'username' => 'required|max:255|unique:users,username,' . $id,
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|confirmed|min:6|max:30',
            'phone' => 'nullable|max:15|unique:users,phone,' . $id,
            'status' => 'in:active,non_active,need_activation',
            'role' => 'required|in:surveyor,admin'
        ]);

        $input = $request->except(['password_confirmation']);

        // if (!empty($input['password'])) {
        //     $input['password'] = app('hash')->make($input['password']);
        // }

        // $input = ['updated_by' => $this->user->id];

        if($this->user->role != 'admin'){
            if($this->user->id != $id){
                return response()->json([
                    'message' => 'Anda tidak berhak untuk mengubah profil pengguna lain!'
                ], 400);
            }


        }

        if ($user) {
            $input['name'] = ucwords($request->name);
            $input['status'] = 'active';
            $input['role'] = 'surveyor';

            if($request->has('password')){
                $input = $request->except(['password_confirmation']);
                $input['password'] = app('hash')->make($input['password']);
            }

            $user->update($input);

            return response()->json([
                'message' => 'Data Pengguna telah berhasil diubah.',
                'data' => $user
            ], 201);
        }

        return response()->json([
            'message' => 'Data Pengguna tidak ditemukan.',
            'data' => $user
        ], 404);
    }

    public function destroy($id)
    {
        if ($this->user->role != 'admin'){
            return response()->json([
                'message' => 'Anda tidak berhak untuk mengakses fungsi ini!'
            ], 400);
        }

        $user = User::find($id);

        if($user) {
            $data = ['deleted_by' => $this->user->id];
            $user->update($data);

            $user->delete();

            return response()->json([
                'message' => 'Data Pengguna berhasil dihapus.'
            ], 200);
        }

        return response()->json([
            'message' => 'Data Pengguna tidak ditemukan!'
        ], 404);
    }

    public function restore($id)
    {
        if ($this->user->role != 'admin'){
            return response()->json([
                'message' => 'Anda tidak berhak untuk mengakses fungsi ini!'
            ], 400);
        }

        $user = User::onlyTrashed()->find($id);

        if($user) {
            $data = ['deleted_by' => null];
            $user->update($data);

            $user->restore();

            return response()->json([
                'message' => 'Data Pengguna berhasil dikembalikan.'
            ], 200);
        }

        return response()->json([
            'message' => 'Data Pengguna tidak ditemukan!'
        ], 404);
    }

    public function change_password(Request $request, $id)
    {
        // if ($this->user->role != 'admin' && $this->user->role != 'pimpinan' && $this->user->role != 'salesman'){
        //     return response()->json([
        //         'message' => 'Anda tidak berhak untuk mengakses fungsi ini!'
        //     ], 400);
        // }

        $user = User::findOrFail($id);

        if($this->user->role != 'admin'){
            if($this->user->id != $id){
                return response()->json([
                    'message' => 'Anda tidak berhak untuk mengubah profil pengguna lain!'
                ], 400);
            }
        }

        $this->validate($request, [
            'password' => 'required|confirmed|min:6|max:30',
        ]);

        $input = $request->except(['password_confirmation']);

        $input['password'] = app('hash')->make($input['password']);

        if ($user) {
            $user->update($input);

            return response()->json([
                'message' => 'Password Pengguna telah berhasil diubah.'
            ], 201);
        }

        return response()->json([
            'message' => 'Data Pengguna tidak ditemukan.'
        ], 404);
    }
}

