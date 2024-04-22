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
use App\Models\ClientUser;
use App\Models\UploadFile;
use App\Models\UploadFolder;
use Auth;

class DocumentController extends Controller
{
    public function index($subproject)  //done check
    {
        try {
            $origin = 'uploads/'. Client::where('client_email', Auth::user()->email)->value('client_id'). '/'. base64_decode($subproject);

            $files = Storage::files($origin);
            $folders = Storage::directories($origin);
            $directorytype = 1; //1 is parent

            //return user list for permission
            $listusers = ClientUser::orderBy('group_id', 'ASC')->where('client_id', \globals::get_client_id())->where('user_id', '!=', Auth::user()->user_id)->get();

            return view('adminuser.document.index', compact('files', 'folders','origin','directorytype','listusers'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
    }

    public function uploadFiles(Request $request)
    {   
        try {
            if ($request->hasFile('files')) {
                $uploadAttempt = 0;
                $maxAttempt = 100;

                do {
                    try {
                        $files = $request->file('files');
                        $pathFile = "";
                        $pathLoop = "";
                        $paths = explode('/', $request->filePath);

                        for ($i = 0; $i < count($paths) - 1; $i++) {
                            $pathFile .= $paths[$i] . '/';
                        }

                        $paths = explode('/', $pathFile);
                        array_unshift($paths, '');
                        array_pop($paths);

                        foreach ($paths as $index => $pathf) {
                            if ($index > 0 ) {
                                $pathLoop .= $folderList[$pathf] . '/';
                            } else {
                                $pathLoop .= $pathf . '/';
                            }
                            
                            $directories = Storage::directories(base64_decode($request->location) . $pathLoop);

                            foreach ($directories as $dir) {
                                $baseName = UploadFolder::where('basename', basename($dir))->value('name');
                                $originalName = basename($dir);
                                
                                $folderList[$baseName] = $originalName;
                            }

                            if ($index > 0) {
                                $paths[$index] = $folderList[$pathf];
                            }
                        }
                        break;
                    } catch (\Exception $e) {
                        $uploadAttempt++;

                        if ($uploadAttempt >= $maxAttempt) {
                            break;
                        }

                        usleep(250);
                    }
                } while ($uploadAttempt <= $maxAttempt);
                
                $pf = implode("/", $paths);

                $response = [];
                foreach ($files as $file) {
                        $locationParts = explode('/', base64_decode($request->location), 3);

                        $path = base64_decode($request->location) .$pf;

                        $filePath = $file->storeAs($path, Str::random(8));

                        $maxIndex = max(UploadFile::where('directory', $path)->max('index'), UploadFolder::where('directory', $path)->max('index'));
                        $fileIndex = $maxIndex == null ? 1 : $maxIndex + 1;

                        UploadFile::create([
                            'index' => $fileIndex,
                            'project_id' => $locationParts[0],
                            'subproject_id' => $locationParts[1],
                            'directory' => $path,
                            'basename' => basename($filePath),
                            'name' => $file->getClientOriginalName(),
                            'client_id' => Client::where('client_email', Auth::user()->email)->value('client_id'),
                            'mime_type' => $file->getClientMimeType(),
                            'size' => $file->getSize(),
                            'status' => 1,
                            'uploaded_by' => Auth::user()->user_id,
                        ]);
                        $response[] = ['path' => $filePath];
                }
            } 
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        return response()->json(['success' => true, 'message' => 'Operation success']);
    }

    public function CreateFolder(Request $request)
    {
        try {
            $directories = Storage::directories('uploads/' . Client::where('client_email', Auth::user()->email)->value('client_id') . '/' . base64_decode($request->location));
            $basename = Str::random(8);
            $originPath = base64_decode($request->location);
            $locationParts = explode('/', $originPath, 5);
            $path = $originPath . '/'. $request->folder_name; 
            $folders = Storage::directories($originPath);

            if (!in_array($request->folder_name, $folders)){
                $maxIndex = max(UploadFile::where('directory', $originPath)->max('index'), UploadFolder::where('directory', $originPath)->max('index'));
                $folderIndex = $maxIndex == null ? 1 : $maxIndex + 1;
        
                UploadFolder::create([
                    'index' => $folderIndex,
                    'project_id' => $locationParts[2],
                    'subproject_id' => $locationParts[3],
                    'directory' => $path,
                    'basename' => $basename,
                    'name' => $request->folder_name,
                    'client_id' => Client::where('client_email', Auth::user()->email)->value('client_id'),
                    'status' => 1,
                    'uploaded_by' => Auth::user()->user_id, 
                ]);
        
                Storage::makeDirectory($path, 0755,true);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
        return back();
    }

    public function folder($folder = null) //done
    {
        try {
            $origin = UploadFolder::where('basename', base64_decode($folder))->value('directory');

            $directory = $origin;

            if (substr_count($origin, '/') > 1) {
                $directorytype = 0; //0 is child
            } else {
                $directorytype = 1;
            }
           
            $files = Storage::files($directory);
            $folders = Storage::directories($directory);

            $listusers = ClientUser::orderBy('group_id', 'ASC')->where('client_id', \globals::get_client_id())->where('user_id', '!=', Auth::user()->user_id)->get();

            return view('adminuser.document.index', compact('files', 'folders', 'origin', 'directorytype', 'listusers'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
    }

    public function DownloadFile($path, $file)
    {
        try {
            $dir = base64_decode($path);
            $data = base64_decode($file);
            $directory = $dir . '/' . $data;

            return Storage::disk('local')->download($directory, UploadFile::where('basename', $data)->value('name'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
    }

    public function rename_file(Request $request)
    {
        try {
            $old_name = $request->old_name;
            $new_name = $request->new_name;
            $extension = pathinfo(UploadFile::where('basename', $old_name)->value('name'), PATHINFO_EXTENSION);
            UploadFile::where('basename', $old_name)->update(['name' => $new_name . '.' . $extension]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
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
        $directory = $origin;
        $search = $request->query('name');
        $allItems = array_merge(Storage::allDirectories($directory), Storage::allFiles($directory));
        $searchFiles = UploadFile::where('name', 'LIKE', '%'.$search.'%')->pluck('basename')->toArray();
        $searchFolders = UploadFolder::where('name', 'LIKE', '%'.$search.'%')->pluck('basename')->toArray();
        $searchQuery = array_merge($searchFiles,$searchFolders);
        $filteredItems = [];

        foreach ($allItems as $item) {
            foreach ($searchQuery as $query) {
                if (strpos($item, $query) !== false) {
                    $filteredItems[] = $item;
                }
            }
        }
    
        $folders = array_filter($filteredItems, function($item) {
            return is_dir(storage_path('app/' . $item));
        });
    
        $files = array_filter($filteredItems, function($item) {
            return is_file(storage_path('app/' . $item));
        });
    
        return view('adminuser.document.search', compact('folders', 'files', 'origin', 'directorytype', 'search'));
    }

    public function multiup(Request $request) {
        $arr = explode(',', $request->paths);
        $dirList = array();
        $result = array();
    
        foreach ($arr as $folder) {
            $directory = explode('/', $folder);
            $ext = "";
            for ($i = 0; $i < count($directory) - 1; $i++) {
                $ext .= $directory[$i] . '/';
            }
            array_push($dirList, $ext);
        }
    
        $dirList = array_unique($dirList);
    
        foreach ($dirList as $path) {
            $parts = array_filter(explode('/', $path));
            $lastPart = end($parts);
            array_pop($parts);
            $temp = &$result;
    
            foreach ($parts as $part) {
                $temp = &$temp[$part];
            }
    
            $temp[$lastPart] = [];
        }
    
        $this->createFolders($result, $request->location);
    }
    
    private function createFolders($array, $location) {
        foreach ($array as $key => $value) {
            if (!empty($key)) {
                $randomString = Str::random(8);

                $locationParts = explode('/', base64_decode($location), 5);

                $originPath =  base64_decode($location);
                $path = base64_decode($location) . '/' . $key;
                
                $maxIndex = max(UploadFile::where('directory', $originPath)->max('index'), UploadFolder::where('directory', $originPath)->max('index'));
                $folderIndex = $maxIndex == null ? 1 : $maxIndex + 1;
        
                UploadFolder::create([
                    'index' => $folderIndex,
                    'project_id' => $locationParts[2],
                    'subproject_id' => $locationParts[3],
                    'directory' => $path,
                    'basename' => $randomString,
                    'name' => $key,
                    'client_id' => Client::where('client_email', Auth::user()->email)->value('client_id'),
                    'status' => 1,
                    'uploaded_by' => Auth::user()->user_id,
                ]);
        
                Storage::makeDirectory($path, 0755, true);
                if (is_array($value)) {
                    $this->createFolders($value, base64_encode(base64_decode($location) . '/' . $key));
                }
            }
        }
    }
}