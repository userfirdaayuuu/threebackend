<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kursus;
use App\Models\Useres;
use Illuminate\Support\Facades\Auth;
use MongoDB\BSON\ObjectId;

class UseresController extends Controller
{
    public function akunSaya(Request $request)
    {
        $user = auth()->user(); // ambil user yang sedang login

        $response = [
            'nama' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        if ($user->role === 'siswa') {
            $response['kursus_diikuti'] = $user->kursus_siswa->map(function($k) {
            return [
                '_id' => (string)$k->_id,
                'namaKursus' => $k->namaKursus,
            ];
        });
        }

    if ($user->role === 'tutor') {
        $response['kursus_diajar'] = $user->kursus_tutor->map(function($k) {
            return [
                '_id' => (string)$k->_id,
                'namaKursus' => $k->namaKursus,
            ];
        });
    }
        return response()->json($response);
    }

    public function getKursusSiswa($id) 
    {
        $user = Auth::user();
        if ($user->role !== 'siswa') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $kursus = $user->kursus_siswa;

        return response()->json([
            'message' => 'Berhasil mengambil data kursus siswa',
            'kursus' => $kursus->map(function ($item) {
                return [
                    'id' => (string) $item->_id,
                    'namaKursus' => $item->namaKursus
                ];
            })
        ]);
    }

    public function kursusSiswa()
    {
        
    $user = Auth::user();

    $kursus = $user->kursus_siswa->map(function($k) {
        return [
            '_id' => (string)$k->_id,
            'namaKursus' => $k->namaKursus
        ];
    });

    return response()->json([
        'message' => 'Berhasil mengambil kursus yang diikuti siswa',
        'kursus' => $kursus
    ]);
}

    public function kursusTutor()
    {
        
    $user = Auth::user();

    $kursus = $user->kursus_tutor->map(function($k) {
        return [
            '_id' => (string)$k->_id,
            'namaKursus' => $k->namaKursus
        ];
    });

    return response()->json([
        'message' => 'Berhasil mengambil kursus yang diajar tutor',
        'kursus' => $kursus
    ]);
}

public function getAllSiswa()
    {
        // Ambil semua user dengan role siswa
        $siswaList = Useres::where('role', 'siswa')->whereNotNull('siswa.kursus')->get();


        return response()->json([
            'success' => true,
            'data' => $siswaList
        ]);
    }
public function getKursusTutor($kursus_id)
{
    $kursus = Kursus::find($kursus_id);

    if (!$kursus || !$kursus->tutor_id) {
        return response()->json([
            'status' => 'error',
            'message' => 'Kursus atau tutor tidak ditemukan'
        ], 404);
    }

    $tutor = Useres::find($kursus->tutor_id);

    if (!$tutor) {
        return response()->json([
            'status' => 'error',
            'message' => 'Tutor tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'tutor' => [
            'id'    => $tutor->id,
            'name'  => $tutor->name,
            'email'=> $tutor->email,
        ]
    ]);

}

public function tandaiMateriSelesai($material_id)
{
    $user = Auth::user(); // ambil user yang sedang login

    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    // Cegah duplikasi ID materi
    if (!in_array($material_id, $user->materi_selesai ?? [])) {
        $user->push('materi_selesai', $material_id, true);
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Materi berhasil ditandai sebagai selesai',
        'materi_selesai' => $user->materi_selesai
    ]);
}

public function getCompletedCount($material_id)
{
    $count = Useres::where('role', 'siswa')
        ->where('materi_selesai', [$material_id])
        ->count();

    return response()->json([
        'success' => true,
        'material_id' => $material_id,
        'completed_count' => $count
    ]);

}
}