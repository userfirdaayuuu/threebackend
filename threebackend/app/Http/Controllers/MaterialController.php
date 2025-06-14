<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\CloudinaryServices;

class MaterialController extends Controller
{
    // ✅ Upload Materi Kursus

        public function upload(Request $request, CloudinaryServices $cloudinary, $kursus_id)
        {
            $request->validate([
                'judul_materi'     => 'required|string|max:255',
                'deskripsi_materi' => 'required|string|max:1000',
                'video_url'        => 'nullable|url',
                'document'         => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            ]);

            $documentUrl = null;

            // Kalau ada file dokumen, upload ke Cloudinary
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $userId = auth()->id(); // opsional
                $uploaded = $cloudinary->uploadDocument($file, 'dokumen', $userId);
                $documentUrl = $uploaded->url;
            }

            // Simpan ke database
            $material = Material::create([
                'kursus_id'        => $kursus_id, 
                'judul_materi'     => $request->judul_materi,
                'deskripsi_materi' => $request->deskripsi_materi,
                'video_url'        => $request->video_url,
                'document'         => $documentUrl, // tambahkan field ini di schema
            ]);

            return response()->json([
                'message'  => 'Materi berhasil diunggah',
                'material' => $material
            ], 201);
        }


    // ✅ Get semua materi
    public function index()
    {
        $materials = Material::all();

        return response()->json([
            'success' => true,
            'data' => $materials
        ]);
    }

    // ✅ Hapus materi
    public function destroy($id)
    {
        $material = Material::find($id);

        if (!$material) {
            return response()->json(['message' => 'Materi tidak ditemukan'], 404);
        }

        // if ($material->dokumen) {
        //     Storage::disk('public')->delete($material->dokumen);
        // }

        $material->delete();

        return response()->json(['message' => 'Materi berhasil dihapus']);
    }

    // ✅ Get materi berdasarkan kursus_id
    public function getByKursus($kursus_id)
    {
        $materials = Material::where('kursus_id', $kursus_id)->with('kursus')->get();

         $materials->transform(function ($item) {
        $item->nama_kursus = $item->kursus->namaKursus ?? '';
        return $item;
    });
    
        return response()->json([
            'success' => true,
            'data' => $materials
        ]);
    }

    public function showDetail($kursus_id, $materialId)
    {
    // contoh pencarian materi
    $material = Material::where('kursus_id', $kursus_id)->where('_id', $materialId)->first();

    if (!$material) {
        return response()->json(['success' => false, 'message' => 'Materi tidak ditemukan'], 404);
    }

    return response()->json([
        'success' => true,
        'data' => $material
    ], 200);
}

    // ✅ Tandai materi selesai oleh user
public function tandaiSelesai($id)
{
    $user = auth()->user();

    $material = Material::find($id);

    if (!$material) {
        return response()->json(['message' => 'Materi tidak ditemukan'], 404);
    }

    $user->materiSelesai()->syncWithoutDetaching([
        $id => ['selesai' => true]
    ]);

    return response()->json(['message' => 'Materi berhasil ditandai selesai']);
}

public function cekSelesai($id)
{
    $user = auth()->user();

    $selesai = $user->materiSelesai()->where('material_id', $id)->first();

    return response()->json([
        'success' => true,
        'selesai' => $selesai ? true : false
    ]);
}

}

