<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FanController extends Controller
{

    public function index()
    {
        $fan = Fan::with('klub')->latest()->get();
        return response()->json([
            'success' => true,
            'message' => 'Daftar Fans',
            'data' => $fan,
        ], 200);
    }


    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nama_fan' => 'required',
            'klub' => 'required|array',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validasi gagal',
                'errors' => $validate->errors(),
            ], 422);
        }

        try {
            $fan = new Fan();
            $fan->nama_fan = $request->nama_fan;
            $fan->save();

            $fan->klub()->attach($request->klub);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasi dibuat',
                'data' => $fan,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 404 );
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
         try{
            $fan = Fan::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Detail Liga',
                'data' => $fan,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'data tidak ada',
                'errors' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validate = Validator::make($request->all(), [
            'nama_fan' => 'required',
            'klub' => 'required|array',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validasi gagal',
                'errors' => $validate->errors(),
            ], 422);
        }

        try {
            $fan = Fan::findOrFail($id);
            $fan = new Fan();
            $fan->nama_fan = $request->nama_fan;
            $fan->save();

            $fan->klub()->sync($request->klub);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasi dirubah',
                'data' => $fan,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 404 );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $fan = Fan::findOrFail($id);
            $fan->klub()->detach();
            $fan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasi dihapus',
                'data' => $fan,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'errors' => $e->getMessage(),
            ], 500 );
        }
    }
}
