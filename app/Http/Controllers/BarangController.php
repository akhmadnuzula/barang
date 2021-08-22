<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    public function __construct()
    {
        
    }
    public function get($page = 0)
    {
        $limit = 10;
        $skip = ($page - 1) * $limit;
        $barang = Barang::select('id', 'nama', 'kategori', 'harga', 'diskon', 'image')->skip($skip)->take($limit)->get();
        $totalRecord = Barang::count();
        $data = [
            'status' => 1,
            'total_record' => $totalRecord,
            'list_barang' => $barang,
        ];
        return Response::json($data, 200);
    }

    public function create(Request $request)
    {
        if(!empty($request->nama) && !empty($request->kategori) && !empty($request->harga)){
            $nama = addslashes($request->nama);
            $kategori = addslashes($request->kategori);
            $harga = addslashes($request->harga);
            $imageName = '';
            if($request->hasFile('image')){
                $file = $request->file('image');
                $ext = $file->extension();
                $arrExt = ['jpg', 'jpeg', 'png'];
                if(in_array($ext, $arrExt)){
                    $imageName = strtotime(date('Y-m-d H:i:s')).$file->getClientOriginalName();
                    if(!Storage::disk('public')->put('images/'.$imageName, $file->get())){
                        $data = [
                            'status' => 0,
                            'message' => 'Gambar gagal diunggah',
                        ];
                        return Response::json($data, 200);
                    }
                }else{
                    $data = [
                        'status' => 0,
                        'message' => 'Hanya bisa upload jpg, jpeg, png',
                    ];
                    return Response::json($data, 200);
                }
            }

            if($harga >= 40000 ){
                $diskon = 10;
            }else if($harga >= 20000 && $harga < 40000){
                $diskon = 5;
            }else{
                $diskon = 0;
            }

            $insert = Barang::create([
                        'nama' => $nama,
                        'kategori' => $kategori,
                        'harga' => $harga,
                        'diskon' => $diskon,
                        'image' => $imageName,
                    ]);
            if($insert){
                $data = [
                    'status' => 1,
                    'message' => 'Berhasil menambah barang',
                ];
            }else{
                $data = [
                    'status' => 0,
                    'message' => 'Gagal menambah barang',
                ];
            }
            return Response::json($data, 200);
        }else{
            $data = [
                'status' => 0,
                'message' => 'Ada form yang kosong',
            ];
            return Response::json($data, 200);
        }
    }


    public function update(Request $request, $id)
    {
        if(!empty($request->nama) && !empty($request->kategori) && !empty($request->harga)){
            $oldData = Barang::find($id);
            $nama = addslashes($request->nama);
            $kategori = addslashes($request->kategori);
            $harga = addslashes($request->harga);
            $imageName = $oldData->image;
            if($request->hasFile('image')){
                $file = $request->file('image');
                $ext = $file->extension();
                $arrExt = ['jpg', 'jpeg', 'png'];
                if(in_array($ext, $arrExt)){
                    $imageName = strtotime(date('Y-m-d H:i:s')).$file->getClientOriginalName();
                    if(!Storage::disk('public')->put('images/'.$imageName, $file->get())){
                        $data = [
                            'status' => 0,
                            'message' => 'Gambar gagal diunggah',
                        ];
                        return Response::json($data, 200);
                    }
                }else{
                    $data = [
                        'status' => 0,
                        'message' => 'Hanya bisa upload jpg, jpeg, png',
                    ];
                    return Response::json($data, 200);
                }
            }

            if($harga > 40000 ){
                $diskon = 10;
            }else if($harga > 20000 && $harga <= 40000){
                $diskon = 5;
            }else{
                $diskon = 0;
            }

            $update = Barang::where('id', $id)->update([
                        'nama' => $nama,
                        'kategori' => $kategori,
                        'harga' => $harga,
                        'diskon' => $diskon,
                        'image' => $imageName,
                    ]);
            if($update){
                $data = [
                    'status' => 1,
                    'message' => 'Berhasil memperbarui barang',
                ];
            }else{
                $data = [
                    'status' => 0,
                    'message' => 'Gagal memperbarui barang',
                ];
            }
            return Response::json($data, 200);
        }else{
            $data = [
                'status' => 0,
                'message' => 'Ada form yang kosong',
            ];
            return Response::json($data, 200);
        }
    }

    public function delete($id){
        $del = Barang::where('id', $id)->delete();
        if($del){
            $data = [
                'status' => 1,
                'message' => 'Berhasil menghapus barang',
            ];
        }else{
            $data = [
                'status' => 0,
                'message' => 'Gagal menghapus barang',
            ];
        }
        return Response::json($data, 200);
    }

}