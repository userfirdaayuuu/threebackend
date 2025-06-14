<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kursus;
use App\Models\Useres;
use MongoDB\BSON\ObjectId;

class KursusController extends Controller
{
    public function store(Request $request)
    {
        
        $request->validate([
            'namaKursus' => 'required|string'
        ]);

        $kursus = Kursus::create([
            'namaKursus' => $request->namaKursus,
        ]);

        return response()->json([
            'message' => 'Kursus berhasil dibuat',
            'data' => $kursus
        ], 201);
    }

    public function index()
    {
        $kursus = Kursus::with('detail')->get();
        return response()->json($kursus);
    }

    public function getSiswaByKursus($id)
    {
        try {
        $kursusId = new ObjectId($id); // harus ObjectId karena data di DB begitu

        $siswa = Useres::where('role', 'siswa')
            ->where('siswa.kursus', $kursusId)
            ->get();

        return response()->json(['siswa' => $siswa]);

    } catch (\Exception $e) {
        return response()->json(['error' => 'ID format salah atau error: ' . $e->getMessage()], 400);
    }
    }



}