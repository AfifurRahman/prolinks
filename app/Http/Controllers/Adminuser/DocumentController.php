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
use App\Models\Permission;
use Auth;

class DocumentController extends Controller
{
    public function Index($subproject)  //done check
    {
        try {
            $origin = 'uploads/'. \globals::get_client_id(). '/'. base64_decode($subproject);
            $permission =  explode('/', base64_decode($subproject), 5);
           

            $files = Storage::files($origin);
            $folders = Storage::directories($origin);
            $directorytype = 1; //1 is parent

            $path = explode('/', base64_decode($subproject), 5);
            $subProjectPath = $path[1];

            $fileList = UploadFile::where('subproject_id', $subProjectPath)->distinct()->pluck('basename');
            $listusers = ClientUser::orderBy('group_id', 'ASC')->where('client_id', \globals::get_client_id())->where('user_id', '!=', Auth::user()->user_id)->get();

            return view('adminuser.document.index', compact('files','folders','origin','directorytype','listusers','fileList'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
    }

    public function CheckPermission(Request $request)
    {
        try {
            $permissionlist = Permission::where('user_id', $request->userid)->get();
            $username = User::where('user_id', $request->userid)->value('email');

            return response()->json(['permissionlist' => $permissionlist, 'username' => $username]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
    }

    public function SetPermission(Request $request)
    {
        try{
            $checkboxStatus = $request->all();

            $testing = "";

            foreach ($checkboxStatus as $checkboxId => $checked) {
                if ($checkboxId != "userid") {
                    if(is_null(Permission::where('user_id', $request->userid)->where('fileid', $checkboxId)->value('permission'))) {
                        if ($checked == "true"){
                            Permission::create([
                                'user_id' => $request->userid,
                                'fileid' => $checkboxId,
                                'permission' => '1',
                            ]);
                        } else {
                            Permission::create([
                                'user_id' => $request->userid,
                                'fileid' => $checkboxId,
                                'permission' => '0',
                            ]);
                        } 
                    } else {
                        if ($checked == "true"){
                            Permission::where('user_id', $request->userid)->where('fileid', $checkboxId)->update(['permission' => '1']);
                        } else {
                            Permission::where('user_id', $request->userid)->where('fileid', $checkboxId)->update(['permission' => '0']);
                        }   
                    }
                }
            }
            return response()->json($testing);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
    }

    public function UploadFiles(Request $request)
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
                        $locationParts = explode('/', base64_decode($request->location), 5);

                        $path = base64_decode($request->location) .$pf;

                        $filePath = $file->storeAs($path, Str::random(8));

                        $maxIndex = max(UploadFile::where('directory', $path)->max('index'), UploadFolder::where('parent', $path)->max('index'));
                        $fileIndex = $maxIndex == null ? 1 : $maxIndex + 1;

                        UploadFile::create([
                            'index' => $fileIndex,
                            'project_id' => $locationParts[2],
                            'subproject_id' => $locationParts[3],
                            'directory' => $path,
                            'basename' => basename($filePath),
                            'name' => $file->getClientOriginalName(),
                            'client_id' => \globals::get_client_id(),
                            'mime_type' => $file->getClientMimeType(),
                            'size' => $file->getSize(),
                            'status' => 1,
                            'uploaded_by' => Auth::user()->user_id,
                        ]);
                        $response[] = ['path' => $filePath];
                }
            } 
            return response()->json(['success' => true, 'message' => 'Files successfully uploaded']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function CreateFolder(Request $request)
    {
        try {
            $currentPath = base64_decode($request->location);
            $basename = Str::random(8);
            $originPath = base64_decode($request->location);
            $locationParts = explode('/', $originPath, 5);
            $path = $originPath . '/'. $request->folderName;

            $folders = UploadFolder::where('parent', $currentPath)->where('name', $request->folderName)->value('name');

            if (is_null($folders)){
                $maxIndex = max(UploadFile::where('directory', $originPath)->max('index'), UploadFolder::where('parent', $originPath)->max('index'));
                $folderIndex = $maxIndex == null ? 1 : $maxIndex + 1;
        
                UploadFolder::create([
                    'index' => $folderIndex,
                    'project_id' => $locationParts[2],
                    'subproject_id' => $locationParts[3],
                    'parent' => $originPath,
                    'directory' => $path,
                    'basename' => $basename,
                    'name' => $request->folderName,
                    'client_id' => \globals::get_client_id(),
                    'status' => 1,
                    'uploaded_by' => Auth::user()->user_id, 
                ]);
        
                Storage::makeDirectory($path, 0755,true);

                return response()->json(['success' => true, 'message' => 'Folder successfully created']);
            } else {
                return response()->json(['success' => false, 'message' => 'Same folder name already exist']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
        
    }

    public function OpenFolder($folder = null) //done
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

            $path = explode('/', $directory, 5);
            $subProjectPath = $path[3];

            $fileList = UploadFile::where('subproject_id', $subProjectPath)->distinct()->pluck('basename');
            $listusers = ClientUser::orderBy('group_id', 'ASC')->where('client_id', \globals::get_client_id())->where('user_id', '!=', Auth::user()->user_id)->get();

            return view('adminuser.document.index', compact('files', 'folders', 'origin', 'directorytype', 'listusers', 'fileList'));
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

    public function RenameFile(Request $request)
    {
        try {
            $old_name = $request->old_name;
            $new_name = $request->new_name;
            $extension = pathinfo(UploadFile::where('basename', $old_name)->value('name'), PATHINFO_EXTENSION);
            UploadFile::where('basename', $old_name)->update(['name' => $new_name . '.' . $extension]);

            return response()->json(['success' => true, 'message' =>'Successfully rename the file']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to rename the file']);
        }
    }

    public function DeleteFile(Request $request)
    {
        try {
            UploadFile::where('basename', base64_decode($request->file))->update(['status' => 0]);

            return response()->json(['success' => true, 'message' => 'File successfully removed']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function RenameFolder(Request $request)
    {
        try {
            $name = $request->name;
            UploadFolder::where('name', $name)->update(['displayname' => $request->newname]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
        return back();
    }

    public function DeleteFolder(Request $request) {
        try {
            $foldername = base64_decode($request->folder);
            UploadFolder::where('directory', 'LIKE', '%'.$foldername.'%')->update(['status' => 0]);
            UploadFile::where('directory', 'LIKE', '%'.$foldername.'%')->update(['status' => 0]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
        return back();
    }

    public function Search(Request $request) {
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

    public function MultipleUpload(Request $request) {
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
                
                $maxIndex = max(UploadFile::where('directory', $originPath)->max('index'), UploadFolder::where('parent', $originPath)->max('index'));
                $folderIndex = $maxIndex == null ? 1 : $maxIndex + 1;

                $folders = UploadFolder::where('name', $request->folder_name)->value('name');
                
                if(is_null($folders)){
                    UploadFolder::create([
                        'index' => $folderIndex,
                        'project_id' => $locationParts[2],
                        'subproject_id' => $locationParts[3],
                        'parent' => $originPath,
                        'directory' => $path,
                        'basename' => $randomString,
                        'name' => $key,
                        'client_id' => \globals::get_client_id(),
                        'status' => 1,
                        'uploaded_by' => Auth::user()->user_id,
                    ]);
            
                    Storage::makeDirectory($path, 0755, true);
                }
                   
                if (is_array($value)) {
                    $this->createFolders($value, base64_encode(base64_decode($location) . '/' . $key));
                }
            }
        }
    }
}