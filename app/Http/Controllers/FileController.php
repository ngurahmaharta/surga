<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Item;

class FileController extends Controller
{
    public function update_pic_item(Request $request , $id){ //parameter : pic1, pic2, pic3
        $item = Item::find($id);

        $this->validate($request, [
            'pic1' => 'nullable|image|mimes:jpeg,jpg,bmp,png|max:10240', // max 10 MB
            'pic2' => 'nullable|image|mimes:jpeg,jpg,bmp,png|max:10240',
            'pic3' => 'nullable|image|mimes:jpeg,jpg,bmp,png|max:10240',
        ]);

        $upload_path = 'images/items';

        if ($request->hasFile('pic1')) {
            $pic1 = $request->file('pic1');
            $ext1 = $pic1->getClientOriginalExtension();

            if ($pic1->isValid()) {
                $pic_name = Carbon::now()->format('YmdHs') . "a." . $ext1;
                // $success = $pic1->move($upload_path, $pic_name);
                $pic1->move($upload_path, $pic_name);
                $item->update(['pic1' => $pic_name]);

                // if(!$success){
                //     return response()->json([
                //         'message' => 'Gagal meng-upload file gambar'
                //     ], 400);
                // }
            }
        }

        if ($request->hasFile('pic2')) {
            $pic2 = $request->file('pic2');
            $ext2 = $pic2->getClientOriginalExtension();

            if ($pic2->isValid()) {
                $pic_name = Carbon::now()->format('YmdHs') . "b." . $ext2;
                $pic2->move($upload_path, $pic_name);
                $item->update(['pic2' => $pic_name]);
            }
        }

        if ($request->hasFile('pic3')) {
            $pic3 = $request->file('pic3');
            $ext3 = $pic3->getClientOriginalExtension();

            if ($pic3->isValid()) {
                $pic_name = Carbon::now()->format('YmdHs') . "c." . $ext3;
                $pic3->move($upload_path, $pic_name);
                $item->update(['pic3' => $pic_name]);
            }
        }

        return response()->json([
            'message' => 'Upload gambar berhasil!'
        ], 201);
    }

    // public function update_pic_item(Request $request, $id){ //parameter : pic1, pic2, pic3
    //     $data = $request->all();
    //     // return 'aaaaaaaaaaaa';

    //     $item = Item::find($id);

    //     // $this->validate($request, [
    //     //     'pic1' => 'nullable|image|mimes:jpeg,jpg,bmp,png|max:10240', // max 10 KB
    //     //     // 'pic2' => 'nullable|image|mimes:jpeg,jpg,bmp,png|max:10240',
    //     //     // 'pic3' => 'nullable|image|mimes:jpeg,jpg,bmp,png|max:10240',
    //     // ]);

    //     $upload_path = 'images/items';

    //     // $data = Input::all();
    //     // $png_url = “perfil-“.time().“.jpg”;
    //     // $path = public_path() . “/img/designs/” . $png_url;
    //     // $img = $data[‘fileo’];
    //     // $img = substr($img, strpos($img, “,”)+1);
    //     // $data = base64_decode($img);
    //     // $success = file_put_contents($path, $data);
    //     // print $success ? $png_url : ‘Unable to save the file.’;

    //     if ($request->has('pic1')) {
    //         // $pic1 = $request->file('pic1');
    //         $pic1 = base64_decode($request->pic1);
    //         $ext1 = $pic1->getClientOriginalExtension();

    //         if ($pic1->isValid()) {
    //             $pic_name = Carbon::now()->format('YmdHs') . "a." . $ext1;
    //             $pic1->move($upload_path, $pic_name);
    //             $item->update(['pic1' => $pic_name]);
    //         }
    //     }

    //     // if ($request->hasFile('pic2')) {
    //     //     $pic2 = $request->file('pic2');
    //     //     $ext2 = $pic2->getClientOriginalExtension();

    //     //     if ($pic2->isValid()) {
    //     //         $pic_name = Carbon::now()->format('YmdHs') . "b." . $ext2;
    //     //         $pic2->move($upload_path, $pic_name);
    //     //         $item->update(['pic2' => $pic_name]);
    //     //     }
    //     // }

    //     // if ($request->hasFile('pic3')) {
    //     //     $pic3 = $request->file('pic3');
    //     //     $ext3 = $pic3->getClientOriginalExtension();

    //     //     if ($pic3->isValid()) {
    //     //         $pic_name = Carbon::now()->format('YmdHs') . "c." . $ext3;
    //     //         $pic3->move($upload_path, $pic_name);
    //     //         $item->update(['pic3' => $pic_name]);
    //     //     }
    //     // }

    //     return response()->json([
    //         'message' => 'Upload gambar berhasil!'
    //     ], 201);
    // }

    // public function read_file(){
    //     $data = Storage::disk('local')->get('file.txt');
    //     var_dump($data);
    // }

    // public function download_file(){
    //     // echo 'hello';
    //     // return Storage::disk('local')->download('file.txt');
    //     // var_dump($data);

    //     // return Storage::disk('public')->download('dummymale.jpg');

    //     $path = storage_path('images/dummymale.jpg');
    //     return $path;

    //     // if (!File::exists($path)) {
    //     //     abort(404);
    //     // }

    //     // $file = File::get($path);
    //     // $type = File::mimeType($path);

    //     // return $type;

    //     // $response = Response::make($file, 200);
    //     // $response->header("Content-Type", $type);

    //     // return $response;
    // }

    // public function write_file(){
    //     // echo 'hello';
    //     Storage::disk('local')->put('file.txt', 'hello world');
    // }

    // private function upload_file(Request $request)
    // {
    //     $now = Carbon::now()->format('YmdHs');
    //     $pic = $request->file('pic1');
    //     $ext  = $pic->getClientOriginalExtension();
    //     return $ext;

    //     if ($pic->isValid()) {
    //         $pic_name   = $now . "." . $ext;
    //         $upload_path = 'images/item';
    //         $pic->move($upload_path, $pic_name);

    //         return $pic_name;
    //     }
    //     return false;
    // }

    // private function delete_file(User $user)
    // {
    //     $exist = Storage::disk('profil_pic')->exists($user->profil_pic);

    //     if (isset($user->profil_pic) && $exist) {
    //         $delete = Storage::disk('profil_pic')->delete($user->profil_pic);
    //         if ($delete) {
    //             return true;
    //         }
    //         return false;
    //     }
    // }


}
