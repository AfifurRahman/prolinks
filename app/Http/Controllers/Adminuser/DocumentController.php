<?php

namespace App\Http\Controllers\Adminuser;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use Auth;

class DocumentController extends Controller
{
    public function index()
    {
        return view('adminuser.document.index');
    }

    public function upload(Request $request)
    {
        try {
            if ($request->hasFile('files')) {
                $files = $request->file('files');
                $response = [];

                foreach ($files as $file) {
                    $path = 'uploads/' . DB::table('clients')->where('client_email', Auth::user()->email)->value('client_id') . '/subproject' . '/';
                    $filePath = $file->store($path);
                    $response[] = ['path' => $filePath];
                }

                return response()->json(['success' => true, 'message' => 'Files uploaded successfully', 'files' => $response]);
            } else {
                return response()->json(['success' => false, 'message' => 'No files uploaded'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error uploading files: ' . $e->getMessage()], 500);
        }
    }
}
