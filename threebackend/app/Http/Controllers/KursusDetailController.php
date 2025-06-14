<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KursusDetail;
use App\Models\Kursus;
use App\Models\Useres;
use MongoDB\BSON\ObjectId;

class KursusDetailController extends Controller
{
    // POST /api/kursus-detail
    public function store(Request $request)
    {
        $request->validate([
            'kursus_id' => 'required|string|unique:kursuses_detail,kursus_id',
            'deskripsi' => 'required|string|max:1000',
            'durasi'    => 'required|string|max:255',
            'modul'     => 'required|string|max:255',
            'jadwal'    => 'required|string|max:255',
            'tutor_id'  => 'required|string',
        ]);

        $data = $request->all();
        $data['kursus_id'] = new ObjectId($data['kursus_id']);
        $data['tutor_id'] = new ObjectId($data['tutor_id']);

        $detail = KursusDetail::create($data);

        return response()->json([
            'message' => 'Detail kursus berhasil disimpan',
            'data' => $detail
        ], 201);
    }

    // GET /api/kursus-detail/{id}
    public function byKursusId($kursus_id)
    {
        try {
            $objectId = new ObjectId($kursus_id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'ID kursus tidak valid.'
            ], 400);
        }

        $detail = KursusDetail::with(['tutor', 'kursus'])
            ->where('kursus_id', $objectId)
            ->first();

        if (!$detail) {
            return response()->json([
                'message' => 'Detail kursus tidak ditemukan.'
            ], 404);
    }

    $kursus = Kursus::find($detail->kursus_id);
    try {
        $tutorId = $detail->tutor_id;

        if (is_string($tutorId)) {
            $tutorId = new ObjectId($tutorId);
        }

        $tutor = Useres::find($tutorId);
    } catch (\Exception $e) {
        $tutor = null;
    }

    return response()->json([
        'nama_kursus' => $kursus?->namaKursus ?? '(Tidak ada nama kursus)',
        'deskripsi' => $detail->deskripsi,
        'durasi' => $detail->durasi,
        'modul' => $detail->modul,
        'jadwal' => $detail->jadwal,
        'nama_tutor' => $tutor?->name ?? 'Tidak tersedia'
    ]);
}

// put
public function updateByKursusId(Request $request, $kursus_id)
{
    try {
        $objectId = new \MongoDB\BSON\ObjectId($kursus_id);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'ID kursus tidak valid.'
        ], 400);
    }

    // Validasi data
    $validated = $request->validate([
        'deskripsi' => 'required|string',
        'durasi' => 'required|string',
        'modul' => 'required|string',
        'jadwal' => 'required|string',
    ]);

    $detail = KursusDetail::where('kursus_id', $objectId)->first();

    if (!$detail) {
        return response()->json([
            'message' => 'Detail kursus tidak ditemukan.'
        ], 404);
    }

    $detail->deskripsi = $validated['deskripsi'];
    $detail->durasi = $validated['durasi'];
    $detail->modul = $validated['modul'];
    $detail->jadwal = $validated['jadwal'];
    $detail->save();

    return response()->json([
        'message' => 'Detail kursus berhasil diperbarui.',
        'data' => $detail
    ]);
}

}
