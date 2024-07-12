<?php

namespace App\Http\Controllers\Adminuser;

use App\Http\Controllers\Controller;
use App\Helpers\GlobalHelper;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Artisan; 
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
use App\Models\DocumentAction;
use App\Models\Permission;
use App\Models\LogViewDocument;
use App\Models\SettingEmailNotification;
use App\Services\WatermarkService;
use Auth;
use ZipArchive;

class DocumentController extends Controller
{
    protected $WatermarkService;

    public function __construct(WatermarkService $WatermarkService)
    {
        $this->WatermarkService = $WatermarkService;
    }

    public function Index($subproject)
    {
        try {
            $origin = 'uploads/'.\globals::get_client_id(). '/'. base64_decode($subproject);
            $directorytype = 1; 
            $permission =  explode('/', base64_decode($subproject), 5);
            $filesWithMetadata = collect(Storage::files($origin))->map(function ($file) {
                return [
                    'path' => $file,
                    'upload_date' => Storage::lastModified($file) 
                ];
            });
            $files = $filesWithMetadata->sortBy('upload_date')->pluck('path')->toArray();
            $folders = Storage::directories($origin);
            $path = explode('/', base64_decode($subproject), 5);
            $subProjectPath = $path[1];
            $projectID = $path[0];
            $subprojectID = $path[1];

            $fileList = UploadFile::where('subproject_id', $subProjectPath)->distinct()->pluck('basename');
            $listusers = ClientUser::orderBy('group_id', 'ASC')->where('client_id', \globals::get_client_id())->where('user_id', '!=', Auth::user()->user_id)->get();

            return view('adminuser.document.index', compact('files','folders','origin','directorytype','listusers','fileList', 'projectID', 'subprojectID'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
    }

    public function CheckPermission(Request $request)
    {
        try {
            if(Auth::user()->type == \globals::set_role_administrator()) { 
                $permissionTable = [];
                $files = [];

                foreach(UploadFile::where('subproject_id', $request->subprojectid)->get('basename') as $file) {
                    $files[] = $file->basename;
                }

                $users = AssignProject::where('client_id', \globals::get_client_id())
                ->where('project_id', $request->projectid)
                ->where('user_id', '!=', Auth::user()->user_id)
                ->get();

                foreach($users as $user) {
                    $permission = [];

                    foreach($files as $file) {
                        if (!is_null(Permission::where('fileid', $file)->where('user_id', $user->user_id)->value('permission'))) {
                            $permission[] = [
                                'id' => $file,
                                'permission' => Permission::where('fileid', $file)->where('user_id', $user->user_id)->value('permission'),
                            ];
                        } else {
                             $permission[] = [
                                'id' => $file,
                                'permission' => '1',
                            ];
                        }
                    }

                    $permissionTable[$user->user_id] = $permission;
                }
                
                return response()->json(['permissionlist' => $permissionTable]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
    }

    public function SetPermission(Request $request)
    {
        try {
            if(Auth::user()->type == \globals::set_role_administrator()) {
                $data = $request->input('permission');

                foreach ($data as $userId => $permissions) {
                    foreach ($permissions as $permissionData) {
                        $fileId = $permissionData['id'];
                        $permissionValue = $permissionData['permission'];
                        
                        if(is_null(Permission::where('user_id', $userId)->where('fileid', $fileId)->value('permission'))) {
                            Permission::create([
                                'user_id' => $userId,
                                'fileid' => $fileId,
                                'permission' => $permissionValue,
                            ]);
                        } else {
                            Permission::where('user_id', $userId)
                                ->where('fileid', $fileId)
                                ->update(['permission' => $permissionValue]);
                        }
                    }
                }

                $desc = Auth::user()->name . " set permission on user " . $request->userid;
                \log::create(request()->all(), "success", $desc);

                return response()->json(['success' => true, 'message' => "Successfully updated the permission"]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function CreateFolder(Request $request)
    {
        try {
            if(Auth::user()->type == \globals::set_role_collaborator() OR Auth::user()->type == \globals::set_role_administrator()) {
                $currentPath = base64_decode($request->location);
                $basename = Str::random(8);
                $originPath = base64_decode($request->location);
                $locationParts = explode('/', $originPath, 5);
                $foldername = $request->folderName;
                $forbiddenchar = ['\\', '/', '*', '?', '"', '<', '>', '|'];
                $fixedname = str_replace($forbiddenchar, ' ', $foldername);
                $path = $originPath . '/'. $fixedname;

                $folders = UploadFolder::where('parent', $currentPath)->where('name', $foldername)->value('name');

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
                        'name' => $fixedname,
                        'displayname' => $foldername,
                        'client_id' => \globals::get_client_id(),
                        'status' => 1,
                        'uploaded_by' => Auth::user()->user_id, 
                    ]);
            
                    $desc = Auth::user()->name . " created folder " . $request->folderName;
                    \log::create(request()->all(), "success", $desc);

                    return response()->json(['success' => true, 'message' => 'Folder ' . $request->folderName . ' has successfully created.']);
                } else {
                    return response()->json(['success' => false, 'message' => 'Same folder name already exist.']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Your role is not allowed to create folder.']);
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
            $projectID = $path[2];
            $subprojectID = $path[3];

            $fileList = UploadFile::where('subproject_id', $subProjectPath)->distinct()->pluck('basename');
            $listusers = ClientUser::orderBy('group_id', 'ASC')->where('client_id', \globals::get_client_id())->where('user_id', '!=', Auth::user()->user_id)->get();

            return view('adminuser.document.index', compact('files', 'folders', 'origin', 'directorytype', 'listusers', 'fileList', 'projectID', 'subprojectID'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Operation failed']);
        }
    }

    public function ViewFile($file) {
        try {
            $file = base64_decode($file);
            
            if (substr_count(UploadFile::where('basename', $file)->value('directory'), '/') <= 3 ) {
                $path = explode('/', UploadFile::where('basename', $file)->value('directory'), 5);
                $projectID = $path[2];
                $subprojectID = $path[3];

                $link = route('adminuser.documents.list', base64_encode($projectID . '/' . $subprojectID));
            } else {
                $link = route('adminuser.documents.openfolder', base64_encode(UploadFolder::where('directory', UploadFile::where('basename', $file)->value('directory'))->value('basename')));
            }

            $mimeType = UploadFile::where('basename', $file)->value('mime_type');

            $desc = Auth::user()->name . " viewed file " . $file . " (" . UploadFile::where('basename', $file)->value('name') . ")";
            \log::create(request()->all(), "success", $desc);
            $this->logViewFile($file);

            if (str_starts_with($mimeType, 'image/')) {
                return view('adminuser.document.viewer.image', compact('file', 'link'));
            } elseif (str_starts_with($mimeType, 'application/pdf')) {
                return view('adminuser.document.viewer.pdf', compact('file', 'link'));
            } else {
                return view('adminuser.document.viewer.error', compact('file', 'link'));
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function WatermarkFile($file) 
    {
        try {
            return back();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function ServeFile($file)
    {
        try {
            $fileID = base64_decode($file);
            $filePath = UploadFile::where('basename', $fileID)->value('directory');
            $fileDirectory = $filePath . '/' . $fileID;
            $fileMimeType = UploadFile::where('basename', $fileID)->value('mime_type');

            if (str_starts_with($fileMimeType, 'application/pdf')) {
                $this->WatermarkService->addPDFWatermark($fileID);

                $filePath = 'temp/'. Auth::user()->user_id;
                $fileDirectory = 'temp/'. Auth::user()->user_id . '/temp';
            }  elseif (str_starts_with($fileMimeType, 'image/')) {
               // $this->WatermarkService->addIMGWatermark($fileID);

               // $filePath = 'temp/'. Auth::user()->user_id;
               // $fileDirectory = 'temp/'. Auth::user()->user_id . '/temp';
            }   

            if (Storage::disk('local')->exists($filePath)) {
                $fileContent = Storage::disk('local')->get($fileDirectory);
                $mimeType = Storage::disk('local')->mimeType($fileDirectory);

                return response($fileContent, 200)
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
            $fileID = base64_decode($file);
            $fileDirectory = UploadFile::where('basename', $fileID)->value('directory') . '/' . $fileID;
            $fileMimeType = UploadFile::where('basename', $fileID)->value('mime_type');

            $fileIndex = '';
            $originPath = implode('/', array_slice(explode('/', UploadFile::where('basename', $fileID)->value('directory')), 0, 4));

            foreach(array_slice(explode('/', UploadFile::where('basename', $fileID)->value('directory')), 4) as $path) {
                $originPath .= '/' . $path;
                $fileIndex .= DB::table('upload_folders')->where('directory', $originPath)->where('name', $path)->value('index') . '.';
            }

            $fileIndex .= DB::table('upload_files')->where('basename', $fileID)->value('index');

            if (str_starts_with($fileMimeType, 'application/pdf')) {
                $this->WatermarkService->addPDFWatermark($fileID);

                return response()->download(Storage::path('temp/'. Auth::user()->user_id . '/temp'), $fileIndex . ' - ' . UploadFile::where('basename', $fileID)->value('name'));
            } elseif (str_starts_with($fileMimeType, 'image/')) {
                //$this->WatermarkService->addIMGWatermark($fileID);

                //return response()->download(Storage::path('temp/'. Auth::user()->user_id . '/temp'), $fileIndex . ' - ' . UploadFile::where('basename', $fileID)->value('name'));
                return response()->download(Storage::path($fileDirectory), $fileIndex . ' - ' . UploadFile::where('basename', $fileID)->value('name'));
            } else {
                return response()->download(Storage::path($fileDirectory), $fileIndex . ' - ' . UploadFile::where('basename', $fileID)->value('name'));
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function DownloadFiles(Request $request) 
    {
        try {
            $SelectedItems = $request->input('files');
            $SelectedItems = explode(',', $SelectedItems);

            $tempZipFile = tempnam(sys_get_temp_dir(), 'folder_zip');
            $zip = new ZipArchive();
            $zip->open($tempZipFile, ZipArchive::CREATE);
            $arr = [];

            $foldername = $SelectedItems;
            foreach($SelectedItems as $item) {
                if (UploadFile::where('basename', base64_decode($item))->exists()) {
                    if (UploadFile::where('basename', base64_decode($item))->value('status') == '1') {
                        $index = '';
                        $filename = base64_decode($item);
                        $originPath = implode('/', array_slice(explode('/', UploadFile::where('basename', $filename)->value('directory')), 0, 4));

                        foreach(array_slice(explode('/', UploadFile::where('basename', $filename)->value('directory')), 4) as $path) {
                            $originPath .= '/' . $path;
                            $index .= UploadFolder::where('directory', $originPath)->where('name', $path)->value('index') . '.';
                        }

                        $index .= UploadFile::where('basename', basename($filename))->value('index');

                        $directory = UploadFile::where('basename', $filename)->value('directory');

                        $fullPath = $directory . '/' . $filename;
                        $originalName = UploadFile::where('basename', $filename)->value('name');

                        $zip->addFile(Storage::path($fullPath), $index . ' - ' .$originalName);
                    }
                } elseif (UploadFolder::where('basename', base64_decode($item))->exists()) {
                    $folderbasename = base64_decode($item);
                    $foldername = UploadFolder::where('basename', $folderbasename)->value('displayname');
                    $folderpath = UploadFolder::where('basename', $folderbasename)->value('parent');
                    $folderdir = UploadFolder::where('basename', $folderbasename)->value('directory');
                    $files = Storage::allFiles($folderdir);
                    $FilePath = '';

                    foreach ($files as $file) {
                        if (UploadFile::where('basename', basename($file))->value('status') == '1') {
                            $index = '';
                            $StoredPath = "";
                            $filename = basename($file);
        
                            $FilePath = UploadFile::where('basename', basename($file))->value('directory');

                            $originPath = implode('/', array_slice(explode('/', $FilePath), 0, 4));
                            foreach(array_slice(explode('/', $FilePath), 4) as $path) {
                                $originPath .= '/' . $path;
                                $index .= UploadFolder::where('directory', $originPath)->where('name', $path)->value('index') . '.';
                            }
                            $index .= UploadFile::where('basename', $filename)->value('index');
                            $filename = $index . ' - ' . UploadFile::where('basename', $filename)->value('name');

                            $OriginFullPath = $folderpath;
                            $LocatedPath = substr($FilePath, strlen($folderpath) + 1);
                           
                            foreach(explode('/', $LocatedPath) as $path) {
                                $index = '';
                                $OriginFullPath .= '/' . $path;

                                $originPath = implode('/', array_slice((explode('/', $folderpath)), 0, 4));
                                $test = '';
                                foreach(array_slice(explode('/', $OriginFullPath), 4) as $pathindex) {
                                    $originPath .= '/' . $pathindex;
                                    $index .= UploadFolder::where('directory', $originPath)->where('name', $pathindex)->value('index') . '.';
                                }
                                $index = rtrim($index, '.');
                                $FixedName = $index . ' - ' . $path;
                                $StoredPath .= $FixedName . '/';
                            }
    
                            $zip->addFile(Storage::path($file), $StoredPath . $filename);
                        }
                    }
                }
            }

            $zip->close();
            $destinationPath =  'temp/'. Auth::user()->user_id . '/temp';
            Storage::put($destinationPath, file_get_contents($tempZipFile));

            $desc = Auth::user()->name . " downloaded files";
            \log::create(request()->all(), "success", $desc);

            return response()->json(['success' => true, 'link' => base64_encode($destinationPath)]);
        } catch (\Exception $e) {
             return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function DownloadZip($link = null) 
    {
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
            if(Auth::user()->type == \globals::set_role_administrator()) {
                $old_name = $request->old_name;
                $new_name = $request->new_name;
                $extension = pathinfo(UploadFile::where('basename', $old_name)->value('name'), PATHINFO_EXTENSION);
                UploadFile::where('basename', $old_name)->update(['name' => $new_name . '.' . $extension]);

                $desc = Auth::user()->name . " renamed file " . $old_name . " to " . $new_name;
                \log::create(request()->all(), "success", $desc);

                return response()->json(['success' => true, 'message' => 'File has successfully renamed to ' . $new_name]);
            } else {
                return response()->json(['success' => false, 'message' => 'Your role is not allowed to rename file.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to rename the file']);
        }
    }

    public function Delete(Request $request) {
        try {
            if(Auth::user()->type == \globals::set_role_administrator()) {
                $item = base64_decode($request->input('item'));

                if (UploadFile::where('basename', $item)->exists()) {
                    UploadFile::where('basename', $item)->update(['status' => 0]);
                    $itemname = UploadFile::where('basename', $item)->value('name');                   
                } elseif (UploadFolder::where('basename', $item)->exists()) {
                    UploadFolder::where('basename', $item)->update(['status' => 0]);
                    $FolderLocation = UploadFolder::where('basename', $item)->value('directory');
                    $itemname = UploadFolder::where('basename', $item)->value('name');

                    UploadFolder::where('parent', 'like', '%'. $FolderLocation .'%')->update(['status' => 0]);
                    UploadFile::where('directory', 'like', '%'. $FolderLocation .'%')->update(['status' => 0]);
                }
                $log = Auth::user()->name . ' deleted ' . $itemname;

                return response()->json(['success' => true, 'message' => 'Successfully removed ' . $itemname . '.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Your role is not allowed to remove file.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function RenameFolder(Request $request)
    {
        try {
            if(Auth::user()->type == \globals::set_role_administrator()) {
                $name = $request->name;
                $location = base64_decode($request->location);
                UploadFolder::where('parent', $location)->where('name', $name)->update(['displayname' => $request->newname]);

                $desc = Auth::user()->name . " renamed folder " . $name . " to " . $request->newname;
                \log::create(request()->all(), "success", $desc);

                return response()->json(['success' => true, 'message' => 'Folder has successfully renamed to ' . $request->newname . '.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Your role is not allowed to rename folder.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function Search(Request $request) 
    {
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

    public function Upload(Request $request) 
    {
        try {
            if(Auth::user()->type == \globals::set_role_collaborator() OR Auth::user()->type == \globals::set_role_administrator()) {
                $saveLocation = base64_decode($request->input('location'));
                $filePaths = $request->input('filePath');
                $projectID = explode('/', $saveLocation, 5);
                $files = $request->file('file');
                $logFilesName = "";
                $emailFilesName = "";
                $emailTotalByte = 0;
                
                foreach ($filePaths as $filePath) {
                    if ($filePath != "") {
                        $filePathParts = explode('/', $filePath);

                        $IndexPathPart = "";
                        $PathPart = "";

                        $IndexPath = explode('/', $filePath);
                        array_unshift($IndexPath, '');
                        array_pop($IndexPath);

                        foreach ($filePathParts as $key => $part) {
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
                    }
                }

                foreach($files as $key => $file) {
                    $remainQuota = (DB::table('pricing')->where('id', DB::table('clients')->where('client_id',\globals::get_client_id())->value('pricing_id'))->value('allocation_size')) - (DB::table('upload_files')->where('client_id', \globals::get_client_id())->sum('size'));
                    $fullFilePath = rtrim($saveLocation . '/' . $filePaths[$key], '/');

                    if (($remainQuota - $file->getSize()) > 0) {
                        $savedFile = $file->storeAs($fullFilePath, Str::random(8));
                        $maxIndex = max(UploadFile::where('directory', $fullFilePath)->max('index'), UploadFolder::where('parent', $fullFilePath)->max('index'));
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
                    $logFilesName .= $file->getClientOriginalName() . " (". basename($savedFile) ."), ";
                    $emailFilesName .= $file->getClientOriginalName() . ", ";
                    $emailTotalByte += $file->getSize();
                    }
                }

                $emailFilesName = rtrim($emailFilesName,  ", ");
                $logFilesName = rtrim($logFilesName, ', ');

                $receiver_email = AssignProject::where('subproject_id', $projectID[3])->where('client_id', \globals::get_client_id())->get();
                $receiver_admin = User::where('client_id', \globals::get_client_id())->where('type', '0')->where('status', '1')->get();

                $desc = Auth::user()->name . " uploaded file " . $logFilesName;
                \log::create(request()->all(), "success", $desc);

                $link = UploadFolder::where('directory', $saveLocation)->value('basename');

                \log::push_notification('New File Added', $type=1, $link, $projectID[3]);

                if (substr_count($saveLocation, '/') <= 3 ) {
                    $url = route('adminuser.documents.list', base64_encode($projectID[2] . '/' . $projectID[3]));
                } else {
                    $url = route('adminuser.documents.openfolder', base64_encode($link));
                }

                if(count($receiver_email) > 0) {
                    foreach ($receiver_email as $key => $value) {
                        if($value->email != Auth::user()->email) {
                            if((User::where('user_id', $value->user_id)->value('status') == '1') && (!is_null(User::where('user_id', $value->user_id)->value('email_verified_at'))) ) {
                                $check_settings = SettingEmailNotification::where('project_id', $value->project_id)->where('subproject_id', $value->subproject_id)->where('client_id', $value->client_id)->where('user_id', $value->user_id)->where('clientuser_id', $value->clientuser_id)->value('is_upload_file');
                                if (!empty($check_settings) && $check_settings == 1) {
                                    $details = [
                                        'receiver' => User::where('user_id',$value->user_id)->value('name'),
                                        'project_name' => Project::where('project_id', $projectID[2])->value('project_name'),
                                        'uploader' => Client::where('client_id', \globals::get_client_id())->value('client_name'),
                                        'file_name' => $emailFilesName,
                                        'file_size' => GlobalHelper::formatBytes($emailTotalByte),
                                        'url' => $url,
                                    ];
                                    \Mail::to($value->email)->send(new \App\Mail\DocumentUploads($details));
                                }
                            }
                        }
                    }
                }

                return response()->json(['success' => true, 'message' => "Successfully uploaded file and folder."]);
            } else {
                return response()->json(['success' => false, 'message' => "Your role is not allowed to upload file or folder."]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        } 
    }

    public function ActionTest() {
        DocumentAction::create([
            'project_id' => '123',
            'subproject_id' => '456',
            'user_id' => Auth::user()->user_id,
            'status' => 1,
            'action_type' => 1,
            'items_basename' => '12345',
        ]);
    }

    public function Cut(Request $request)
    {
        try {
            $ItemBasename = $request->input('items');
            if (UploadFile::where('basename', $ItemBasename)->exists()) {
                $ProjectID = UploadFile::where('basename', $ItemBasename)->value('project_id');
                $SubProjectID = UploadFile::where('basename', $ItemBasename)->value('subproject_id');
            } else {
                $ProjectID = UploadFolder::where('basename', $ItemBasename)->value('project_id');
                $SubProjectID = UploadFolder::where('basename', $ItemBasename)->value('subproject_id');
            }
            
            if ( DocumentAction::where('user_id', Auth::user()->user_id)->exists() )  {
                DocumentAction::where('user_id', Auth::user()->user_id)->update([
                    'project_id' => $ProjectID,
                    'subproject_id' => $SubProjectID,
                    'status' => 1,
                    'action_type' => 2,
                    'items_basename' => $ItemBasename,
                ]);
            } else {
                DocumentAction::create([
                    'project_id' => $ProjectID,
                    'subproject_id' => $SubProjectID,
                    'user_id' => Auth::user()->user_id,
                    'status' => 1,
                    'action_type' => 2,
                    'items_basename' => $ItemBasename,
                ]);
            };
        
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function Copy(Request $request) 
    {
        try {
            $ItemBasename = $request->input('items');
            if (UploadFile::where('basename', $ItemBasename)->exists()) {
                $ProjectID = UploadFile::where('basename', $ItemBasename)->value('project_id');
                $SubProjectID = UploadFile::where('basename', $ItemBasename)->value('subproject_id');
            } else {
                $ProjectID = UploadFolder::where('basename', $ItemBasename)->value('project_id');
                $SubProjectID = UploadFolder::where('basename', $ItemBasename)->value('subproject_id');
            }
            
            if ( DocumentAction::where('user_id', Auth::user()->user_id)->exists() )  {
                DocumentAction::where('user_id', Auth::user()->user_id)->update([
                    'project_id' => $ProjectID,
                    'subproject_id' => $SubProjectID,
                    'status' => 1,
                    'action_type' => 1,
                    'items_basename' => $ItemBasename,
                ]);
            } else {
                DocumentAction::create([
                    'project_id' => $ProjectID,
                    'subproject_id' => $SubProjectID,
                    'user_id' => Auth::user()->user_id,
                    'status' => 1,
                    'action_type' => 1,
                    'items_basename' => $ItemBasename,
                ]);
            };
           
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
        
    }

    public function Paste(Request $request)
    {
        try {
            $ItemBasename = DocumentAction::where('user_id', Auth::user()->user_id)->value('items_basename');
            $PasteLocation = $request->input('location');
            $PasteBasename = Str::random(8);

            if((DocumentAction::where('user_id', Auth::user()->user_id)->value('status') == '1') && (DocumentAction::where('user_id', Auth::user()->user_id)->value('action_type') == '1')) {
                if (UploadFile::where('basename', $ItemBasename)->exists()) {
                    $ProjectID = UploadFile::where('basename', $ItemBasename)->value('project_id');
                    $SubProjectID = UploadFile::where('basename', $ItemBasename)->value('subproject_id');
                    $ItemDirectory = UploadFile::where('basename', $ItemBasename)->value('directory');
                    $ItemName = UploadFile::where('basename', $ItemBasename)->value('name');
                    $ItemMime = UploadFile::where('basename', $ItemBasename)->value('mime_type');
                    $ItemSize = UploadFile::where('basename', $ItemBasename)->value('size');
    
                    Storage::copy($ItemDirectory . '/' . $ItemBasename, $PasteLocation . '/' . $PasteBasename);
    
                    $Index = max(UploadFile::where('directory', $PasteLocation)->max('index'), UploadFolder::where('parent', $PasteLocation)->max('index'));
                    $Index = $Index == null ? 1 : $Index + 1;
    
                    UploadFile::create([
                        'index' => $Index,
                        'project_id' => $ProjectID,
                        'subproject_id' => $SubProjectID,
                        'directory' => $PasteLocation,
                        'basename' => $PasteBasename,
                        'name' => $ItemName,
                        'client_id' => \globals::get_client_id(),
                        'mime_type' => $ItemMime,
                        'size' => $ItemSize,
                        'status' => 1,
                        'uploaded_by' => Auth::user()->user_id,
                    ]);
    
                    DocumentAction::where('user_id', Auth::user()->user_id)->update(['status' => '0']);
                } else {
                    $ProjectID = UploadFolder::where('basename', $ItemBasename)->value('project_id');
                    $SubProjectID = UploadFolder::where('basename', $ItemBasename)->value('subproject_id');
                    $ItemDirectory = UploadFolder::where('basename', $ItemBasename)->value('directory');
                    $ItemName = UploadFolder::where('basename', $ItemBasename)->value('displayname');
                }
            } elseif ((DocumentAction::where('user_id', Auth::user()->user_id)->value('status') == '1') && (DocumentAction::where('user_id', Auth::user()->user_id)->value('action_type') == '2')) {
                if (UploadFile::where('basename', $ItemBasename)->exists()) {
                    $ProjectID = UploadFile::where('basename', $ItemBasename)->value('project_id');
                    $SubProjectID = UploadFile::where('basename', $ItemBasename)->value('subproject_id');
                    $ItemDirectory = UploadFile::where('basename', $ItemBasename)->value('directory');
    
                    Storage::move($ItemDirectory . '/' . $ItemBasename, $PasteLocation . '/' . $ItemBasename);
    
                    $Index = max(UploadFile::where('directory', $PasteLocation)->max('index'), UploadFolder::where('parent', $PasteLocation)->max('index'));
                    $Index = $Index == null ? 1 : $Index + 1;

                    UploadFile::where('basename', $ItemBasename)->update([
                        'index' => $Index,
                        'directory' => $PasteLocation,
                    ]);
    
                    DocumentAction::where('user_id', Auth::user()->user_id)->update(['status' => '0']);
                } else {
                    $ProjectID = UploadFolder::where('basename', $ItemBasename)->value('project_id');
                    $SubProjectID = UploadFolder::where('basename', $ItemBasename)->value('subproject_id');
                    $ItemDirectory = UploadFolder::where('basename', $ItemBasename)->value('directory');
                    $ItemName = UploadFolder::where('basename', $ItemBasename)->value('displayname');
                }
            }

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function ClearClipboard()
    {
        try {
            DocumentAction::where('user_id', Auth::user()->user_id)->update(['status' => '0']);

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function logViewFile($file_id) 
    {
        $getDoc = UploadFile::where('basename', $file_id)->first();

        if (!empty($getDoc->id)) {
            $logDoc = new LogViewDocument;
            $logDoc->client_id = $getDoc->client_id;
            $logDoc->user_id = Auth::user()->user_id;
            $logDoc->project_id = $getDoc->project_id;
            $logDoc->subproject_id = $getDoc->subproject_id;
            $logDoc->document_id = $file_id;
            $logDoc->document_name = $getDoc->name;
            $logDoc->created_by = Auth::user()->id;
            $logDoc->save();
        }
    }

    public function clear_view_cache()
    {
        Artisan::call('view:clear');

        return back();
    }

    
}