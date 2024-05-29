<?php

namespace App\Http\Controllers\Adminuser;

use App\Http\Controllers\Controller;
use App\Helpers\GlobalHelper;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\ClientUser;
use App\Models\UploadFile;
use App\Models\UploadFolder;
use App\Models\AssignProject;
use App\Models\Permission;
use App\Models\SettingEmailNotification;
use Auth;
use ZipArchive;

class DocumentController extends Controller
{
    public function Index($subproject)  //done check
    {
        try {
            $origin = 'uploads/'.\globals::get_client_id(). '/'. base64_decode($subproject);
            $permission =  explode('/', base64_decode($subproject), 5);
           

            $filesWithMetadata = collect(Storage::files($origin))->map(function ($file) {
                return [
                    'path' => $file,
                    'upload_date' => Storage::lastModified($file) 
                ];
            });

            $files = $filesWithMetadata->sortBy('upload_date')->pluck('path')->toArray();

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
            $username = ClientUser::where('user_id', $request->userid)->value('name');
            
            if ( User::where('user_id', $request->userid)->value('type') == 0 ) {
                $role = 'Administrator';
            } else if ( User::where('user_id', $request->userid)->value('type') == 1 ) {
                $role ='Collaborator';
            } else {
                $role = 'Client';
            }

            return response()->json(['permissionlist' => $permissionlist, 'username' => $username . ' - ' . $role]);
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
            $desc = Auth::user()->name . " set permission on user " . $request->userid;
            \log::create(request()->all(), "success", $desc);

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
                        $remainQuota = (DB::table('pricing')->where('id', DB::table('clients')->where('client_id',\globals::get_client_id())->value('pricing_id'))->value('allocation_size')) - (DB::table('upload_files')->where('client_id', \globals::get_client_id())->sum('size'));

                        if (($remainQuota - $file->getSize()) > 0) {
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

                            $receiver_email = AssignProject::where('subproject_id', $locationParts[3])->where('client_id', \globals::get_client_id())->get();
                            $receiver_admin = User::where('client_id', \globals::get_client_id())->where('type', '0')->where('status', '1')->get();

                            $desc = Auth::user()->name . " uploaded file " . $file->getClientOriginalName();
                            \log::create(request()->all(), "success", $desc);
            
                            // \log::push_notification('New File Added', $type=1, basename($filePath), $locationParts[3]);

                            $link = array_slice(explode('/', $path), 2);
                            $link = implode('/', $link);
                            

                            // if(count($receiver_admin) > 0) {
                            //     foreach ($receiver_admin as $key => $value) {
                            //         if($value->email != Auth::user()->email) {
                            //             $details = [
                            //                 'receiver' => $value->name,
                            //                 'project_name' => Project::where('project_id', $locationParts[2])->value('project_name'),
                            //                 'uploader' => Client::where('client_id', \globals::get_client_id())->value('client_name'),
                            //                 'file_name' => $file->getClientOriginalName() ,
                            //                 'file_size' => GlobalHelper::formatBytes($file->getSize()),
                            //                 'url' => route('adminuser.documents.list', base64_encode($link)),
                            //             ];
                            //             \Mail::to($value->email)->send(new \App\Mail\DocumentUploads($details));
                            //         }
                            //     }
                            // }

                            if(count($receiver_email) > 0) {
                                foreach ($receiver_email as $key => $value) {
                                    if($value->email != Auth::user()->email) {
                                        if(User::where('user_id', $value->user_id)->value('status') == '1') {
                                            $check_settings = SettingEmailNotification::where('project_id', $value->project_id)->where('subproject_id', $value->subproject_id)->where('client_id', $value->client_id)->where('user_id', $value->user_id)->where('clientuser_id', $value->clientuser_id)->value('is_upload_file');
                                            if (!empty($check_settings) && $check_settings == 1) {
                                                $details = [
                                                    'receiver' => User::where('user_id',$value->user_id)->value('name'),
                                                    'project_name' => Project::where('project_id', $locationParts[2])->value('project_name'),
                                                    'uploader' => Client::where('client_id', \globals::get_client_id())->value('client_name'),
                                                    'file_name' => $file->getClientOriginalName() ,
                                                    'file_size' => GlobalHelper::formatBytes($file->getSize()),
                                                    'url' => route('adminuser.documents.list', base64_encode($link)),
                                                ];
                                                \Mail::to($value->email)->send(new \App\Mail\DocumentUploads($details));
                                            }
                                        }
                                    }
                                }
                            }

                        } else {
                            return response()->json(['success' => false, 'message' => 'You dont have sufficient quota']);
                        }
                }
            } 
            return response()->json(['success' => true, 'message' => 'Files successfully uploaded ']);
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
                Storage::makeDirectory($path, 0755,true);

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
                    'displayname' => $request->folderName,
                    'client_id' => \globals::get_client_id(),
                    'status' => 1,
                    'uploaded_by' => Auth::user()->user_id, 
                ]);
        
                $desc = Auth::user()->name . " created folder " . $request->folderName;
                \log::create(request()->all(), "success", $desc);

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
           
            $filesWithMetadata = collect(Storage::files($origin))->map(function ($file) {
                return [
                    'path' => $file,
                    'upload_date' => Storage::lastModified($file) 
                ];
            });

            $files = $filesWithMetadata->sortBy('upload_date')->pluck('path')->toArray();

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

    public function ViewFile($file) {
        try {
            $file = base64_decode($file);
            $path = UploadFile::where('basename', $file)->value('directory');

            $link = array_slice(explode('/', $path), 2);
            $link = implode('/', $link);

            $mimeType = UploadFile::where('basename', $file)->value('mime_type');

            $desc = Auth::user()->name . " viewed file " . $file;
            \log::create(request()->all(), "success", $desc);

            if (str_starts_with($mimeType, 'image/')) {
                return view('adminuser.document.viewer.image', compact('file', 'link'));
            }
            elseif (str_starts_with($mimeType, 'application/pdf')) {
                return view('adminuser.document.viewer.pdf', compact('file', 'link'));
            } else {
                return back();
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function ServeFile($file)
    {
        try {
            $file = base64_decode($file);
            $path = UploadFile::where('basename', $file)->value('directory');

            if (Storage::disk('local')->exists($path)) {
                $fileContents = Storage::disk('local')->get($path . '/' . $file);
                $mimeType = Storage::disk('local')->mimeType($path . '/' . $file);

                return response($fileContents, 200)
                    ->header('Content-Type', $mimeType);
            } else {
                abort(404);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function DownloadFile($file)
    {
        try {
            $file = base64_decode($file);
            $directory = UploadFile::where('basename', $file)->value('directory');
            $fullPath = $directory . '/' . $file;

            $index = '';
            $originPath = implode('/', array_slice(explode('/', UploadFile::where('basename', $file)->value('directory')), 0, 4));

            foreach(array_slice(explode('/', UploadFile::where('basename', $file)->value('directory')), 4) as $path) {
                $originPath .= '/' . $path;
                $index .= DB::table('upload_folders')->where('directory', $originPath)->where('name', $path)->value('index') . '.';
            }

            $index .= DB::table('upload_files')->where('basename', basename($file))->value('index');

            $desc = Auth::user()->name . " downloaded file " . $file;
            \log::create(request()->all(), "success", $desc);

            return Storage::disk('local')->download($fullPath, $index . ' - ' . UploadFile::where('basename', $file)->value('name'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function DownloadFiles(Request $request) {
        try {
            $files = $request->input('files');
            $files = explode(',', $files);

            $tempZipFile = tempnam(sys_get_temp_dir(), 'folder_zip');
            $zip = new ZipArchive();
            $zip->open($tempZipFile, ZipArchive::CREATE);
            $arr = [];
            foreach($files as $file) {
                $index = '';
                $filename = base64_decode($file);
                $originPath = implode('/', array_slice(explode('/', UploadFile::where('basename', $filename)->value('directory')), 0, 4));

                foreach(array_slice(explode('/', UploadFile::where('basename', $filename)->value('directory')), 4) as $path) {
                    $originPath .= '/' . $path;
                    $index .= DB::table('upload_folders')->where('directory', $originPath)->where('name', $path)->value('index') . '.';
                }

                $index .= DB::table('upload_files')->where('basename', basename($filename))->value('index');

                $directory = UploadFile::where('basename', $filename)->value('directory');

                $fullPath = $directory . '/' . $filename;
                $originalName = UploadFile::where('basename', $filename)->value('name');

                $zip->addFile(Storage::path($fullPath), $index . ' - ' .$originalName);
            }

            $zip->close();
            
            $destinationPath = 'downloads/'. Auth::user()->user_id . '/temp.zip';

            Storage::put($destinationPath, file_get_contents($tempZipFile));

            $desc = Auth::user()->name . " downloaded files";
            \log::create(request()->all(), "success", $desc);

            return response()->json(['success' => true, 'link' => base64_encode($destinationPath)]);
        } catch (\Exception $e) {
             return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function DownloadZip($link = null) {
        try {

            return Storage::disk('local')->download(base64_decode($link), 'files.zip');
        } catch ( \Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function DownloadFolder($folder)
    {
        try {
            $folderName = base64_decode($folder);
            $files = Storage::allFiles($folderName);
            $tempZipFile = tempnam(sys_get_temp_dir(), 'folder_zip');
            $zip = new ZipArchive();
            $zip->open($tempZipFile, ZipArchive::CREATE);
            $fileName = "";
            $fileFolder = "";
            $log = '';

            foreach ($files as $file) {
                $index = '';

                $relativePath = substr($file, strlen($folderName) + 1);

                $Path = explode('/', $relativePath);
                array_pop($Path);
                $Path = implode('/', $Path);

                $basenameFile = explode('/', $relativePath);
                $basenameFile = end($basenameFile);
                

                if (Auth::user()->type == '0' ||     ((UploadFile::where('basename', $basenameFile)->value('status') == '1') && ((Permission::where('user_id', Auth::user()->user_id)->where('fileid', $basenameFile)->value('permission') == '1') || is_null(Permission::where('user_id', Auth::user()->user_id)->where('fileid', $basenameFile)->value('permission'))))) {
                    
                    $pathFile = UploadFile::where('basename', $basenameFile)->value('directory');

                    $originPath = implode('/', array_slice(explode('/', UploadFile::where('basename', $basenameFile)->value('directory')), 0, 4));
                    foreach(array_slice(explode('/', UploadFile::where('basename', $basenameFile)->value('directory')), 4) as $path) {
                        $originPath .= '/' . $path;
                        $index .= DB::table('upload_folders')->where('directory', $originPath)->where('name', $path)->value('index') . '.';
                    }
                    
                    $index .= DB::table('upload_files')->where('basename', basename($basenameFile))->value('index');
                    $basenameFile = $index . ' - ' .UploadFile::where('basename', $basenameFile)->value('name');

                    if ($Path == "") {
                        $fixedPath = $basenameFile;
                    } else {
                        $FullPath = '';
                        $OriginFullPath = $folderName;

                        foreach(explode('/', $Path) as $paths) {
                            $index = '';
                            
                            $OriginFullPath .= '/' . $paths;
                            
                            $originPath = implode('/', array_slice(explode('/', UploadFolder::where('directory', $OriginFullPath)->value('parent')), 0, 4));

                            foreach(array_slice(explode('/', UploadFolder::where('directory', $OriginFullPath)->value('parent')), 4) as $path) {
                                $originPath .= '/' . $path;
                                
                                $index .= DB::table('upload_folders')->where('directory', $originPath)->where('name', $path)->value('index') . '.';
                            }

                            $index .= DB::table('upload_folders')->where('directory', $OriginFullPath)->value('index');

    
                            $Path = $index . ' - ' . $paths;

                            
                            $log .= $OriginFullPath;

                            $FullPath .= $Path . '/';
                        }
                        

                        $fixedPath =  $FullPath . $basenameFile;
                    }  
                    
                    $zip->addFile(Storage::path($file), $fixedPath);
                }
            }


            $zip->close();
            
            $destinationPath = 'downloads/'. Auth::user()->user_id . '/temp.zip'; 
            Storage::put($destinationPath, file_get_contents($tempZipFile));

            $desc = Auth::user()->name . " downloaded folder path " . $folder;
            \log::create(request()->all(), "success", $desc);

            return Storage::disk('local')->download($destinationPath, basename($folderName) . '.zip');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function RenameFile(Request $request)
    {
        try {
            $old_name = $request->old_name;
            $new_name = $request->new_name;
            $extension = pathinfo(UploadFile::where('basename', $old_name)->value('name'), PATHINFO_EXTENSION);
            UploadFile::where('basename', $old_name)->update(['name' => $new_name . '.' . $extension]);

            $desc = Auth::user()->name . " renamed file " . $old_name . " to " . $new_name;
            \log::create(request()->all(), "success", $desc);

            return response()->json(['success' => true, 'message' =>'Successfully rename the file']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to rename the file']);
        }
    }

    public function DeleteFile(Request $request)
    {
        try {
            UploadFile::where('basename', base64_decode($request->file))->update(['status' => 0]);

            $desc = Auth::user()->name . " deleted file " . base64_decode($request->file);
            \log::create(request()->all(), "success", $desc);

            return response()->json(['success' => true, 'message' => 'File successfully removed']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function RenameFolder(Request $request)
    {
        try {
            $name = $request->name;
            $location = base64_decode($request->location);
            UploadFolder::where('parent', $location)->where('name', $name)->update(['displayname' => $request->newname]);

            $desc = Auth::user()->name . " renamed folder " . $name . " to " . $request->newname;
            \log::create(request()->all(), "success", $desc);

            return response()->json(['success' => true, 'message' => 'Folder successfully renamed']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function DeleteFolder(Request $request) {
        try {
            $foldername = base64_decode($request->folder);
            UploadFolder::where('directory', $foldername)->update(['status' => 0]);

            $desc = Auth::user()->name . " deleted folder " . $foldername;
            \log::create(request()->all(), "success", $desc);

            return response()->json(['success' => true, 'message' =>'Successfully removed the folder']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
    }

    public function Search(Request $request) {
        $origin = base64_decode($request->query('origin'));
        $directorytype = 1;
        $directory = $origin;
        $search = $request->query('name');
        $allItems = array_merge(Storage::allDirectories($directory), Storage::allFiles($directory));
        $searchFiles = UploadFile::where('name', 'LIKE', '%'.$search.'%')->pluck('basename')->toArray();
        $searchFolders = UploadFolder::where('name', 'LIKE', '%'.$search.'%')->pluck('name')->toArray();
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
        try {
            $saveLocation = base64_decode($request->input('location'));
            $filePath = $request->input('filePath');
            $fullFilePath = rtrim($saveLocation . '/' . $filePath, '/');
            $file = $request->file('file');
            $filePathPart = explode('/', $filePath);
            $projectID = explode('/', $saveLocation, 5);
            $PathPart = "";

            $IndexPath = explode('/', $filePath);
            array_unshift($IndexPath, '');
            array_pop($IndexPath);

            $IndexPathPart = "";

            foreach ($filePathPart as $key => $part) {
                $PathPart .= '/' . $part;
               
                $saveDir = rtrim($saveLocation . $PathPart, '/') ;
                $isExist = UploadFolder::where('directory', $saveDir)->value('name');

                if ($key > 0) {
                    $IndexPathPart .= '/' . $IndexPath[$key];
                } 

                $IndexFullPath = rtrim($saveLocation, '/') . $IndexPathPart;

                $maxIndex = max(UploadFile::where('directory', $IndexFullPath)->max('index'), UploadFolder::where('parent', $IndexFullPath)->max('index'));
                $folderIndex = $maxIndex == null ? 1 : $maxIndex + 1;

                if (is_null($isExist)) {
                    Storage::makeDirectory($saveDir, 0755, true);
                    
                    UploadFolder::create([
                        'index' => $folderIndex,
                        'project_id' => $projectID[2],
                        'subproject_id' => $projectID[3],
                        'parent' => $IndexFullPath,
                        'directory' => $saveDir,
                        'basename' => Str::random(8),
                        'name' => $part,
                        'displayname' => $part,
                        'client_id' => \globals::get_client_id(),
                        'status' => 1,
                        'uploaded_by' => Auth::user()->user_id, 
                    ]);
                }
            }
            
            $remainQuota = (DB::table('pricing')->where('id', DB::table('clients')->where('client_id',\globals::get_client_id())->value('pricing_id'))->value('allocation_size')) - (DB::table('upload_files')->where('client_id', \globals::get_client_id())->sum('size'));

            if (($remainQuota - $file->getSize()) > 0) {
                $savedFile = $file->storeAs($fullFilePath, Str::random(8));
                $maxIndex = max(UploadFile::where('directory', $IndexFullPath)->max('index'), UploadFolder::where('parent', $IndexFullPath)->max('index'));
                $fileIndex = $maxIndex == null ? 1 : $maxIndex + 1;

                UploadFile::create([
                    'index' => $fileIndex,
                    'project_id' => $projectID[2],
                    'subproject_id' => $projectID[3],
                    'directory' => $fullFilePath,
                    'basename' => basename($savedFile),
                    'name' => $file->getClientOriginalName(),
                    'client_id' => \globals::get_client_id(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'status' => 1,
                    'uploaded_by' => Auth::user()->user_id,
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        
        
    }
}