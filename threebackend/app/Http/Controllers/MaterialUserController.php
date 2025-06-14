<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaterialUser;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;

class MaterialUserController extends Controller
{
    public function tandaiSelesai(Request $request, $id)
    {
        if (!Material::where('_id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Materi tidak ditemukan.',
            ], 404);
        }

        $useres = Auth::guard('api')->user(); // pastiin guard-nya bener juga ya

        $progress = MaterialUser::updateOrCreate(
            [
                'useres_id' => $useres->id,
                'material_id' => $id,
            ],
            [
                'selesai' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Materi ditandai selesai!',
            'data' => $progress,
        ]);
    }

    public function cekStatusSelesai($id)
{
    $useres = auth()->user();
    $progress = MaterialUser::where('useres_id', $useres->id)
        ->where('material_id', $id)
        ->first();

    return response()->json([
        'success' => true,
        'selesai' => $progress ? $progress->selesai : false,
    ]);
}
public function countSelesai($materialId)
{
    $count = MaterialUser::where('material_id', $materialId)
                ->where('selesai', true)
                ->count();

    return response()->json([
        'success' => true,
        'material_id' => $materialId,
        'completed_count' => $count,
    ]);
}
}
