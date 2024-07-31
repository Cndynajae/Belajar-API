<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pemain;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PemainController extends Controller
{
    public function index()
    {
         $pemain = Pemain::latest()->get();
        $res = [
            'success' => true,
            'message' => 'Daftar Pemain',
            'data' => $pemain,
        ];
        return response()->json($res, 200);
    }

    public function store(Request $request)
   {
        $validator = Validator::make($request->all(), [
            'nama_pemain' => 'required',
            'foto' => 'required|image|mimes:png,jpg',
            'tgl_lahir' => 'required',
            'harga_pasar' => 'required|numeric',
            'posisi' => 'required|in:gk,df,mf,fw',
            'negara' => 'required',
            'id_klub' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'data tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // upload image
            $path = $request->file('foto')->store('public/foto');
            $pemain = new Pemain;
            $pemain->nama_pemain = $request->nama_pemain;
            $pemain->foto = $path;
            $pemain->tgl_lahir = $request->tgl_lahir;
            $pemain->harga_pasar = $request->harga_pasar;
            $pemain->posisi = $request->posisi;
            $pemain->negara = $request->negara;
            $pemain->id_klub = $request->id_klub;
            $pemain->save();
            return response()->json([
                'success' => true,
                'message' => 'data berhasil dibuat',
                'data' => $pemain,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, string $id)
     {
        $validator = Validator::make($request->all(), [
            'nama_pemain' => 'required',
            'foto' => 'required|image|max:2048',
            'id_liga' => 'required',
        ]);

         if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'data tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
             $klub = Pemain::findOrFail($id);
            if($request->hasFile('logo')) {
                // delete logo/foto lama
                Storage::delete($klub->logo);

                $path = $request->file('logo')->store('public/logo');
                $klub->logo = $path;
            }
            $klub->update($request->only(['nama_klub', 'id_liga']));
            return response()->json([
                'success' => true,
                'message' => 'data berhasil dirubah',
                'data' => $klub,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try{
            $pemain = Pemain::findOrFail($id);
            Storage::delete($pemain->foto);
            $pemain->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data' . $pemain->nama_pemain . 'berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'data tidak ada',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }
}
