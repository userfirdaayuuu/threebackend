<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Useres;
use App\Models\Kursus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use MongoDB\BSON\ObjectId;

class RegisterController extends Controller
{
    public function register(Request $request)
{
    $role = $request->input('role');

    // 🔒 Cegah user sembarangan daftar sebagai admin
    if ($role === 'admin') {
        if ($request->input('admin_token') !== env('ADMIN_REGISTER_TOKEN')) {
            return response()->json([
                'message' => 'Unauthorized to register as admin.'
            ], 403);
        }
    }

    // ✅ Validasi dasar
    $rules = [
        'name'     => 'required|string|max:255',
        'email'    => 'required|email',
        'password' => 'required|string|min:6',
        'role'     => 'required|in:siswa,tutor,admin',
    ];

    // 📝 Validasi tambahan (opsional, kalau mau)
    if ($role === 'siswa') {
            $rules['siswa.kelas']  = 'required|string';
            $rules['siswa.kursus'] = [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    foreach ($value as $id) {
                        if (!Kursus::where('_id', new ObjectId($id))->exists()) {
                            $fail("Kursus dengan ID $id tidak ditemukan.");
                        }
                    }
                }
            ];
            $rules['siswa.no_hp'] = 'required|string';
        } elseif ($role === 'tutor') {
            $rules['tutor.kursus_ajar'] = [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    foreach ($value as $id) {
                        if (!Kursus::where('_id', new ObjectId($id))->exists()) {
                            $fail("Kursus dengan ID $id tidak ditemukan.");
                        }
                    }
                }
            ];
    }

    // $kursus = Kursus::firstOrCreate(
    //     ['namaKursus' => $request->input('siswa.kursus')],
    //     ['namaKursus' => $request->input('tutor.kursus_ajar')]
    // );
    $validated = $request->validate($rules);
    //  Convert string ID to ObjectId
    if ($role === 'siswa') {
        $validated['siswa']['kursus'] = array_map(fn($id) => new ObjectId($id), $validated['siswa']['kursus']);
    } else if ($role === 'tutor') {
        $validated['tutor']['kursus_ajar'] = array_map(fn($id) => new ObjectId($id), $validated['tutor']['kursus_ajar']);
    }

    $user = Useres::create([
        'name'     => $validated['name'],
        'email'    => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role'     => $validated['role'],
        'siswa' => $role === 'siswa' ? $validated['siswa'] : null,
        'tutor'   => $role === 'tutor' ? $validated['tutor'] : null,
    ]);

    return response()->json([
        'message' => 'User registered successfully',
        'user'    => $user
    ], 201);
}

}