<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use App\Models\Item;
use App\Models\ItemPrice;
use App\Models\Store;
use App\Http\Resources\ItemList as ItemListResource;
use App\Http\Resources\ItemDetail as ItemDetailResource;
// use App\Models\User;
use Carbon\Carbon;
use DB;

class ItemController extends Controller
{
    protected $jwt;

    public function __construct(JWTAuth $jwt){
        $this->jwt = $jwt;
        $this->user = $this->jwt->user();
    }

    public function index(Request $request){
        // parameter: keyword, per_page
        // sortBy: name, created_at, like, view, price
        // orderBy: asc, desc
        $list_item = Item::with('store', 'item_price')->latest();

        if($request->keyword != ''){
            $keyword = $request->keyword;
            $list_item = $list_item->where(function ($query) use ($keyword){
                $query->where('name', 'like', '%' . $keyword . '%')
                ->orWhere('desc', 'like', '%' . $keyword . '%')
                // ->orWhere('store_name', 'like', '%' . $keyword . '%')
                ->orWhere('tags', 'like', '%' . $keyword . '%');
            })
            ->orderBy('name', 'asc');
        }

        $per_page = $request->has('per_page') ? $per_page = $request->per_page : $per_page = 15;
        $list_item = $per_page == 'all' ? $list_item->get() : $list_item->paginate((int)$per_page);

        return ItemListResource::collection($list_item);
    }

    public function get_random(Request $request){
        $per_page = $request->has('per_page') ? $per_page = $request->per_page : $per_page = 15;
        $list_item = Item::with('store', 'item_price')->orderByRaw('RAND()')->take($per_page)->get();

        return ItemListResource::collection($list_item);
    }

    public function get_my_item(Request $request){
        $per_page = $request->has('per_page') ? $per_page = $request->per_page : $per_page = 15;
        $list_item = Item::with('store', 'item_price')->where('created_by', $this->user->id)->latest()->take($per_page)->get();

        return ItemListResource::collection($list_item);
    }

    public function get_by_store(Request $request, $slug){
        $store = Store::where('slug', $slug)->first();
        $per_page = $request->has('per_page') ? $per_page = $request->per_page : $per_page = 15;
        $list_item = Item::with('store', 'item_price')->where('id_store', $store->id)->latest()->take($per_page)->get();

        return ItemListResource::collection($list_item);
    }

    public function show($slug){
        $item = Item::with('store', 'creator', 'item_price')->where('slug', $slug)->first();

        $item->increment('views');
        return new ItemDetailResource($item);
    }

    public function item_price($id){
        $prices = ItemPrice::where('id_item', $id)->latest()->get();

        return $prices;
    }

    public function store(Request $request){
        if($this->user->role != 'admin' && $this->user->role != 'surveyor'){
            return response()->json([
                'message' => 'Anda tidak berhak untuk menambah data barang!'
            ], 400);
        }

        $this->validate($request, [
            'id_store' => 'required',
            'name' => 'required|max:255',
            'price' => 'required',
            'desc' => 'nullable',
            'tags' => 'nullable',
            'pic1' => 'nullable|image|mimes:jpeg,jpg,bmp,png|max:10240', // max 10 MB
        ]);

        $input = $request->all();
        $input['name'] = ucwords($request->name);
        $input['created_by'] = $this->user->id;
        $input['views'] = 0;
        $input['likes'] = 0;

        try {
            $item = Item::create($input);

            DB::table('users')->where('id', $this->user->id)->increment('points',1);
            // $user = User::find($this->user->id)->increment('points');
            // $user->timestamps = false;
            // $user->increment('popularity');
            // $user->save();

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        if($request->has('price')){
            $input['id_item'] = $item->id;
            ItemPrice::create($input);
        }

        if ($request->hasFile('pic1')) {
            $upload_path = 'images/items';
            $pic1 = $request->file('pic1');
            $ext1 = $pic1->getClientOriginalExtension();

            if ($pic1->isValid()) {
                $pic_name = Carbon::now()->format('YmdHs') . "a." . $ext1;
                $pic1->move($upload_path, $pic_name);
                $item->update(['pic1' => $pic_name]);
            }
        }

        return response()->json([
            'message' => 'Data Item berhasil disimpan.'
        ], 201);
    }

    public function update(Request $request, $id){
        if($this->user->role != 'admin' && $this->user->role != 'surveyor'){
            return response()->json([
                'message' => 'Anda tidak berhak untuk mengubah data barang ini!'
            ], 400);
        }

        $item = Item::find($id);

        if($this->user->role == 'surveyor' && $item->created_by != $this->user->id){
            return response()->json([
                'message' => 'Anda tidak berhak untuk mengubah data barang ini!'
            ], 404);
        }

        $this->validate($request, [
            'name' => 'required|max:255',
            'price' => 'nullable',
            'desc' => 'nullable',
            'tags' => 'nullable',
            'pic1' => 'nullable|image|mimes:jpeg,jpg,bmp,png|max:10240', // max 10 MB
        ]);


        $input = $request->all();
        $input = $request->except(['id_store', 'slug', 'likes', 'views']);

        $input['id_store'] = $item->id_store;
        $input['slug'] = $item->slug;
        $input['name'] = ucwords($request->name);
        $input['updated_by'] = $this->user->id;




        if ($item) {
            $item->update($input);

            DB::table('users')->where('id', $this->user->id)->increment('points',1);

            if($request->has('price')){
                $input['id_item'] = $item->id;
                ItemPrice::create($input);
            }

            if ($request->hasFile('pic1')) {
                $upload_path = 'images/items';
                $pic1 = $request->file('pic1');
                $ext1 = $pic1->getClientOriginalExtension();

                if ($pic1->isValid()) {
                    $pic_name = Carbon::now()->format('YmdHs') . "a." . $ext1;
                    $pic1->move($upload_path, $pic_name);
                    $item->update(['pic1' => $pic_name]);
                }
            }

            return response()->json([
                'message' => 'Data Item telah berhasil diubah.'
            ], 201);
        }

        return response()->json([
            'message' => 'Item tidak ditemukan.'
        ], 404);
    }

    public function update_price(Request $request, $id){
        if($this->user->role != 'admin' && $this->user->role != 'surveyor'){
            return response()->json([
                'message' => 'Anda tidak berhak untuk mengubah data barang ini!'
            ], 400);
        }

        $item = Item::find($id);

        if($this->user->role == 'surveyor' && $item->created_by != $this->user->id){
            return response()->json([
                'message' => 'Anda tidak berhak untuk mengubah data barang ini!'
            ], 404);
        }

        $this->validate($request, [
            'price' => 'required',
        ]);

        $input = $request->all();
        $input['id_item'] = $id;
        $input['created_by'] = $this->user->id;

        try {
            ItemPrice::create($input);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json([
            'message' => 'Data harga berhasil disimpan.'
        ], 201);
    }

    public function destroy($id){
        if($this->user->role != 'admin' && $this->user->role != 'surveyor'){
            return response()->json([
                'message' => 'Anda tidak berhak untuk menghapus data barang ini!'
            ], 400);
        }

        $item = Item::find($id);

        if($this->user->role == 'surveyor' && $item->created_by != $this->user->id){
            return response()->json([
                'message' => 'Anda tidak berhak untuk menghapus data barang ini!'
            ], 404);
        }

        if($item) {
            $data = ['deleted_by' => $this->user->id];
            $item->update($data);
            $item->delete();

            return response()->json([
                'message' => 'Data Item berhasil dihapus.'
            ], 200);
        }

        return response()->json([
            'message' => 'Item tidak ditemukan!'
        ], 404);
    }

    public function restore($id){
        if($this->user->role != 'admin'){
            return response()->json([
                'message' => 'Anda tidak berhak untuk mengakses fungsi ini!'
            ], 400);
        }

        $item = Item::onlyTrashed()->find($id);

        if($item) {
            $data = ['deleted_by' => null];
            $item->update($data);
            $item->restore();

            return response()->json([
                'message' => 'Data Barang berhasil dikembalikan.'
            ], 200);
        }

        return response()->json([
            'message' => 'Barang tidak ditemukan!'
        ], 404);
    }

}
