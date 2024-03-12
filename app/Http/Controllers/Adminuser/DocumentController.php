<?php

namespace App\Http\Controllers\Adminuser;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use App\Models\UploadFile;
use App\Models\UploadFolder;
use Auth;

class DocumentController extends Controller
{
    public function index()
    {
        $origin = "";
        $directory = 'uploads/'. Client::where('client_email', Auth::user()->email)->value('client_id'). '/subproject';
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
                    $path = 'uploads/' . Client::where('client_email', Auth::user()->email)->value('client_id') . '/subproject' . '/'. base64_decode($request->location);
                    $filePath = $file->storeAs($path,Str::random(8));
                    UploadFile::create([
                        'directory' => $path,
                        'basename' => basename($filePath),
                        'name' => $file->getClientOriginalName(),
                        'access_user' => Auth::user()->email,
                        'mime_type' => $file->getClientMimeType(),
                        'size' => $file->getSize(),
                        'status' => 1,
                        'uploaded_by' => Auth::user()->user_id,
                    ]);
                    $response[] = ['path' => $filePath];
                }
            } else {
                
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
        return response()->json(['success' => true, 'message' => 'Operation success']);
    }

    public function create_folder(Request $request)
    {
        $basename = Str::random(8);

        $path = 'uploads/' . Client::where('client_email', Auth::user()->email)->value('client_id') . '/subproject' . '/' . base64_decode($request->location) . $basename; 

        UploadFolder::create([
            'directory' => base64_decode($request->location) . $basename,
            'basename' => $basename,
            'name' => $request->folder_name,
            'access_user' => Auth::user()->email,
            'status' => 1,
            'uploaded_by' => Auth::user()->user_id, 
        ]);

        Storage::makeDirectory($path, 0755,true);

        return back();
    }

    public function folder($folder = null)
    {
        $origin = base64_decode($folder);

        $directory = 'uploads/'. Client::where('client_email', Auth::user()->email)->value('client_id'). '/subproject' . '/' . $origin;

        $files = Storage::files($directory);
        $folders = Storage::directories($directory);

        return view('adminuser.document.index', compact('files', 'folders', 'origin'));
    }

    public function file($path, $file)
    {
        $directory = 'uploads/'. DB::table('clients')->where('client_email', Auth::user()->email)->value('client_id'). '/subproject' . '/' . base64_decode($path);

        return Storage::disk('local')->download($directory, UploadFile::where('basename', base64_decode($file))->value('name'));
    }

    public function delete_file($file)
    {
        UploadFile::where('basename', $file)->update(['status' => 0]);

        return back();
    }

    public function rename_folder(Request $request)
    {
        $old_name = base64_decode($request->old_name);
        UploadFolder::where('basename', $old_name)->update(['name' => $request->new_name]);

        return back();
    }

    public function delete_folder($folder) {

        $foldername = base64_decode($folder);
        UploadFolder::where('directory', 'LIKE', '%'.$foldername.'%')->update(['status' => 0]);
        UploadFile::where('directory', 'LIKE', '%'.$foldername.'%')->update(['status' => 0]);

        return back();
    }
}