<?php

namespace App\Http\Controllers\Adminuser;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use Auth;

class DocumentController extends Controller
{
    public function index()
    {
        $origin = "";
        $directory = 'uploads/'. DB::table('clients')->where('client_email', Auth::user()->email)->value('client_id'). '/subproject';
        $files = Storage::files($directory);
        $folders = Storage::directories($directory);

        return view('adminuser.document.index', compact('files', 'folders','origin'));
    }

    public function upload(Request $request)
    {   
        try {
            if ($request->hasFile('files')) {
                $files = $request->file('files');
                $response = [];

                foreach ($files as $file) {
                    $path = 'uploads/' . DB::table('clients')->where('client_email', Auth::user()->email)->value('client_id') . '/subproject' . '/'. base64_decode($request->location);
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

    public function create_folder(Request $request)
    {
        $path = 'uploads/' . DB::table('clients')->where('client_email', Auth::user()->email)->value('client_id') . '/subproject' . '/' . base64_decode($request->location) . $request->folder_name; 

        Storage::makeDirectory($path, 0755,true);

        return back();
    }

    public function folder($folder = null)
    {
        $origin = base64_decode($folder);

        $directory = 'uploads/'. DB::table('clients')->where('client_email', Auth::user()->email)->value('client_id'). '/subproject' . '/' . $origin;

        $files = Storage::files($directory);
        $folders = Storage::directories($directory);

        return view('adminuser.document.index', compact('files', 'folders', 'origin'));
    }

    public function file($file)
    {
        $directory = 'uploads/'. DB::table('clients')->where('client_email', Auth::user()->email)->value('client_id'). '/subproject' . '/' . base64_decode($file);

        return Storage::disk('local')->download($directory);
    }

}