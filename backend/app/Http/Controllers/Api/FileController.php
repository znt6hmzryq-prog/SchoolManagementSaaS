<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,docx,jpg,png|max:10240', // 10MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        $schoolId = $request->user()->school_id;
        $path = $file->store("school_files/{$schoolId}", 'public');

        $fileRecord = File::create([
            'school_id' => $schoolId,
            'uploaded_by' => $request->user()->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return response()->json($fileRecord, 201);
    }

    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;
        $files = File::where('school_id', $schoolId)->get();

        return response()->json($files);
    }

    public function destroy(Request $request, File $file)
    {
        if ($file->school_id !== $request->user()->school_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return response()->json(['message' => 'File deleted']);
    }
}
