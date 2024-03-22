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
    public function index($subproject)
    {
        try {
            $origin = base64_decode($subproject);
            $directory = 'uploads/'. Client::where('client_email', Auth::user()->email)->value('client_id'). '/'. $origin;
            $files = Storage::files($directory);
            $folders = Storage::directories($directory);
            $directorytype = 1; //1 is parent

            return view('adminuser.document.index', compact('files', 'folders','origin','directorytype'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
    }

    public function uploadFiles(Request $request)
    {   
        try {
            if ($request->hasFile('files')) {
                $files = $request->file('files');
                $response = [];
    
                foreach ($files as $file) {
                        // Handle file upload
                        $locationParts = explode('/', base64_decode($request->location), 3);

                        $path = 'uploads/' . Client::where('client_email', Auth::user()->email)->value('client_id') . '/'. base64_decode($request->location) . '/';
                        $filePath = $file->storeAs($path, Str::random(8));
                        UploadFile::create([
                            'project_id' => $locationParts[0] . '/' . $locationParts[1],
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
            } 
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
        return response()->json(['success' => true, 'message' => 'Operation success']);
    }

    public function uploadFolders(Request $request)
    {
        try {
            $basename = Str::random(8);

            $path = 'uploads/' . Client::where('client_email', Auth::user()->email)->value('client_id') . '/subproject' . '/' . base64_decode($request->location) . $basename; 
    
            UploadFolder::create([
                'project_id' => "HELLO",
                'basename' => $basename,
                'name' => $request->folder_name,
                'access_user' => Auth::user()->email,
                'status' => 1,
                'uploaded_by' => Auth::user()->user_id, 
            ]);
    
            Storage::makeDirectory($path, 0755,true);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
        return back();
    }

    public function create_folder(Request $request)
    {
        try {
            $basename = Str::random(8);

            $path = 'uploads/' . Client::where('client_email', Auth::user()->email)->value('client_id') . '/' . base64_decode($request->location) . '/'. $basename; 
    
            UploadFolder::create([
                'project_id' => "HELLO",
                'basename' => $basename,
                'name' => $request->folder_name,
                'access_user' => Auth::user()->email,
                'status' => 1,
                'uploaded_by' => Auth::user()->user_id, 
            ]);
    
            Storage::makeDirectory($path, 0755,true);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
        return back();
    }

    public function folder($folder = null)
    {
        try {
            $origin = base64_decode($folder);

            $directory = 'uploads/'. Client::where('client_email', Auth::user()->email)->value('client_id'). '/' . $origin;
            if (substr_count($origin, '/') > 1) {
                $directorytype = 0; //0 is child
            } else {
                $directorytype = 1;
            }
           
            $files = Storage::files($directory);
            $folders = Storage::directories($directory);

            return view('adminuser.document.index', compact('files', 'folders', 'origin', 'directorytype'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
    }

    public function file($path, $file)
    {
        try {
            $dir = base64_decode($path);
            $data = base64_decode($file);
            $directory = 'uploads/'. DB::table('clients')->where('client_email', Auth::user()->email)->value('client_id') . '/' .$dir . '/' . $data;

            return Storage::disk('local')->download($directory, UploadFile::where('basename', $data)->value('name'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
    }

    public function delete_file($file)
    {
        try {
            UploadFile::where('basename', $file)->update(['status' => 0]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
        return back();
    }

    public function rename_folder(Request $request)
    {
        try {
            $old_name = base64_decode($request->old_name);
            UploadFolder::where('basename', $old_name)->update(['name' => $request->new_name]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
        return back();
    }

    public function delete_folder($folder) {
        try {
            $foldername = base64_decode($folder);
            UploadFolder::where('directory', 'LIKE', '%'.$foldername.'%')->update(['status' => 0]);
            UploadFile::where('directory', 'LIKE', '%'.$foldername.'%')->update(['status' => 0]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
        return back();
    }

    public function search(Request $request) {
        $origin = base64_decode($request->query('origin'));
        $directorytype = 1;
        $directory = 'uploads/'. Client::where('client_email', Auth::user()->email)->value('client_id'). '/' . $origin;
        $search = $request->query('name');
        // Get all folders and files from the directory
        $allItems = array_merge(Storage::allDirectories($directory), Storage::allFiles($directory));
    
        // Search query
        $searchFiles = UploadFile::where('name', 'LIKE', '%'.$search.'%')->pluck('basename')->toArray();
        $searchFolders = UploadFolder::where('name', 'LIKE', '%'.$search.'%')->pluck('basename')->toArray();

        $searchQuery = array_merge($searchFiles,$searchFolders);
        // Filter items based on the search query
        $filteredItems = [];

        foreach ($allItems as $item) {
            foreach ($searchQuery as $query) {
                // Check if the item name contains the search query
                if (strpos($item, $query) !== false) {
                    $filteredItems[] = $item;
                    // Once a match is found, no need to continue searching with other queries
                }
            }
        }
    
        // Separate folders and files
        $folders = array_filter($filteredItems, function($item) {
            return is_dir(storage_path('app/' . $item));
        });
    
        $files = array_filter($filteredItems, function($item) {
            return is_file(storage_path('app/' . $item));
        });
    
        // Return the results as needed
        return view('adminuser.document.search', compact('folders', 'files', 'origin', 'directorytype', 'search'));
    }
}