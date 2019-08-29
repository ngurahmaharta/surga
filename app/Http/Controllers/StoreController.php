<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use App\Models\Store;
use App\Http\Resources\StoreList as StoreListResource;
use App\Http\Resources\StoreDetail as StoreDetailResource;
use Carbon\Carbon;
use DB;

class StoreController extends Controller
{
    protected $jwt;

    public function __construct(JWTAuth $jwt){
        $this->jwt = $jwt;
        $this->user = $this->jwt->user();
    }

    public function get_random(Request $request){
        $per_page = 3;
        $list_store = Store::withCount('item')->orderByRaw('RAND()');
        $per_page = $request->has('per_page') ? $per_page = $request->per_page : $per_page = 'all';
        $list_store = $per_page == 'all' ? $list_store->get() : $list_store->paginate((int)$per_page);

        return StoreListResource::collection($list_store);
    }

    public function get_my_store(Request $request){
        $per_page = 3;
        $list_store = Store::withCount('item')->where('created_by', $this->user->id)->latest();
        $per_page = $request->has('per_page') ? $per_page = $request->per_page : $per_page = 'all';
        $list_store = $per_page == 'all' ? $list_store->get() : $list_store->paginate((int)$per_page);

        return StoreListResource::collection($list_store);
    }

    public function index(Request $request){
        $per_page = 5;
        // parameter: keyword
        // sortBy: name, time
        // orderBy: asc, desc
        $list_store = Store::withCount('item')->latest();

        $per_page = $request->has('per_page') ? $per_page = $request->per_page : $per_page = 'all';
        $list_store = $per_page == 'all' ? $list_store->get() : $list_store->paginate((int)$per_page);

        return StoreListResource::collection($list_store);
    }

    public function show($slug){
        $store = Store::where('slug', $slug)->withCount('item')->first();
        $store->increment('views');
        // return $store;
        return new StoreDetailResource($store);
    }

    public function store(Request $request){
        if($this->user->role != 'admin' && $this->user->role != 'surveyor'){
            return response()->json([
                'message' => 'Anda tidak berhak untuk menambah data Toko!'
            ], 400);
        }

        $this->validate($request, [
            'name' => 'required|max:255',
            'address' => 'required',
        ]);

        $input = $request->all();
        $input['name'] = ucwords($request->name);
        $input['created_by'] = $this->user->id;
        $input['views'] = 0;
        $input['likes'] = 0;

        try {
            $store = Store::create($input);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        if ($request->hasFile('pic1')) {
            $upload_path = 'images/stores';
            $pic1 = $request->file('pic1');
            $ext1 = $pic1->getClientOriginalExtension();

            if ($pic1->isValid()) {
                $pic_name = Carbon::now()->format('YmdHs') . "a." . $ext1;
                $pic1->move($upload_path, $pic_name);
                $store->update(['pic1' => $pic_name]);
            }
        }

        return response()->json([
            'message' => 'Data Toko berhasil disimpan.'
        ], 201);
    }

    public function update(Request $request, $id){
        if($this->user->role != 'admin' && $this->user->role != 'surveyor'){
            return response()->json([
                'message' => 'Anda tidak berhak untuk mengubah data Toko ini!'
            ], 400);
        }

        $store = Store::find($id);

        if($this->user->role == 'surveyor' && $store->created_by != $this->user->id){
            return response()->json([
                'message' => 'Anda tidak berhak untuk mengubah data Toko ini!'
            ], 404);
        }

        $this->validate($request, [
            'name' => 'required|max:255',
            'address' => 'required',
        ]);

        $input = $request->all();
        $input['name'] = ucwords($request->name);
        $input['updated_by'] = $this->user->id;
        $input['latitude'] = $store->latitude;
        $input['longitude'] = $store->longitude;

        if ($store) {
            $store->update($input);

            if ($request->hasFile('pic1')) {
                $upload_path = 'images/stores';
                $pic1 = $request->file('pic1');
                $ext1 = $pic1->getClientOriginalExtension();

                if ($pic1->isValid()) {
                    $pic_name = Carbon::now()->format('YmdHs') . "a." . $ext1;
                    $pic1->move($upload_path, $pic_name);
                    $store->update(['pic1' => $pic_name]);
                }
            }

            return response()->json([
                'message' => 'Data Toko telah berhasil diubah.'
            ], 201);
        }

        return response()->json([
            'message' => 'Toko tidak ditemukan.'
        ], 404);
    }

    public function destroy($id){
        if($this->user->role != 'admin' && $this->user->role != 'surveyor'){
            return response()->json([
                'message' => 'Anda tidak berhak untuk menghapus data Toko ini!'
            ], 400);
        }

        $store = Store::find($id);

        if($this->user->role == 'surveyor' && $store->created_by != $this->user->id){
            return response()->json([
                'message' => 'Anda tidak berhak untuk menghapus data Toko ini!'
            ], 404);
        }

        if($store) {
            $data = ['deleted_by' => $this->user->id];
            $store->update($data);
            $store->delete();

            return response()->json([
                'message' => 'Data Toko berhasil dihapus.'
            ], 200);
        }

        return response()->json([
            'message' => 'Toko tidak ditemukan!'
        ], 404);
    }

    public function restore($id){
        if($this->user->role != 'admin'){
            return response()->json([
                'message' => 'Anda tidak berhak untuk mengakses fungsi ini!'
            ], 400);
        }

        $store = Store::onlyTrashed()->find($id);

        if($store) {
            $data = ['deleted_by' => null];
            $store->update($data);
            $store->restore();

            return response()->json([
                'message' => 'Data Barang berhasil dikembalikan.'
            ], 200);
        }

        return response()->json([
            'message' => 'Barang tidak ditemukan!'
        ], 404);
    }

}
