<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CloudinaryServices;
use App\Models\Dokumens; 


class CloudinaryController extends Controller
{
    protected CloudinaryServices $cloudinary;

    public function __construct(CloudinaryServices $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    public function uploadDocument(Request $request, CloudinaryServices $cloudinary)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $file = $request->file('document');
        $userId = auth()->id(); // kalau pakai autentikasi

        $dokumen = $cloudinary->uploadDocument($file, 'dokumen', $userId);

        return response()->json([
            'message' => 'Upload sukses',
            'url' => $dokumen->url,
            'public_id' => $dokumen->public_id,
        ]);
    }


    public function destroy(Request $request)
    {
        $request->validate([
            'public_id' => 'required|string',
        ]);

        $this->cloudinary->delete($request->public_id);

        return response()->json(['message' => 'Image deleted.']);
    }

    public function index()
{
    $dokumens = Dokumens::latest()->get();

    return response()->json([
        'success' => true,
        'data' => $dokumens,
    ]);
}
}