<?php

namespace App\Http\Controllers\Adminuser;

use App\Http\Controllers\Controller;
use App\Models\AssignProject;
use App\Models\HistoryImportDiscussion;
use App\Models\Permission;
use App\Models\SubProject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Models\Discussion;
use App\Models\DiscussionComment;
use App\Models\DiscussionAttachFile;
use App\Models\DiscussionLinkFile;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Project;
use App\Models\UploadFile;
use App\Models\UploadFolder;
use Auth;
use Session;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportQuestions;

class DiscussionController extends Controller
{
    function index() {
        if (Auth::user()->type == \globals::set_role_administrator()) {
            $all_questions = Discussion::orderBy('id', 'DESC')->where('client_id', \globals::get_client_id())->where('deleted', 0)->get();
            $unanswered = Discussion::orderBy('id', 'DESC')->where('client_id', \globals::get_client_id())->where('status', \globals::set_qna_status_unanswered())->where('deleted', 0)->get();
            $answered = Discussion::orderBy('id', 'DESC')->where('client_id', \globals::get_client_id())->where('status', \globals::set_qna_status_answered())->where('deleted', 0)->get();
            $closed = Discussion::orderBy('id', 'DESC')->where('client_id', \globals::get_client_id())->where('status', \globals::set_qna_status_closed())->where('deleted', 0)->get();
        }else{
            $all_questions = Discussion::orderBy('id', 'DESC')->where('subproject_id', Auth::user()->session_project)->where('client_id', \globals::get_client_id())->where('deleted', 0)->get();
            $unanswered = Discussion::orderBy('id', 'DESC')->where('subproject_id', Auth::user()->session_project)->where('client_id', \globals::get_client_id())->where('status', \globals::set_qna_status_unanswered())->where('deleted', 0)->get();
            $answered = Discussion::orderBy('id', 'DESC')->where('subproject_id', Auth::user()->session_project)->where('client_id', \globals::get_client_id())->where('status', \globals::set_qna_status_answered())->where('deleted', 0)->get();
            $closed = Discussion::orderBy('id', 'DESC')->where('subproject_id', Auth::user()->session_project)->where('client_id', \globals::get_client_id())->where('status', \globals::set_qna_status_closed())->where('deleted', 0)->get();
        }
        $project = Project::orderBy('id','DESC')->where('client_id', \globals::get_client_id())->where('project_status', \globals::set_project_status_active())->where('parent', '!=', 0)->get();
        $file = Permission::select('upload_files.id', 'upload_files.name')->join('upload_files', 'upload_files.basename', 'permissions.fileid')->where('permissions.user_id', Auth::user()->user_id)->where('permissions.permission', 1)->get();
        return view('adminuser.discussion.index', compact('all_questions', 'unanswered', 'answered', 'closed', 'project', 'file'));
    }

    function detail($discussion_id){
        if (Auth::user()->type == \globals::set_role_administrator()) {
            $detail = Discussion::where('discussion_id', $discussion_id)->where('client_id', \globals::get_client_id())->where('deleted', 0)->first();
        }else{
            $detail = Discussion::where('discussion_id', $discussion_id)->where('subproject_id', Auth::user()->session_project)->where('client_id', \globals::get_client_id())->where('deleted', 0)->first();
        }
        
        $file = Permission::select('upload_files.id', 'upload_files.name')->join('upload_files', 'upload_files.basename', 'permissions.fileid')->where('permissions.user_id', Auth::user()->user_id)->where('permissions.permission', 1)->get();

        return view('adminuser.discussion.detail', compact('discussion_id', 'detail', 'file'));
    }

    function save_discussion(Request $request){
        $results = [];
        try {
            \DB::beginTransaction();

            $id = $request->input('discussion_id');
            $discussion_id = $id;
            if ($id != NULL) {
                $update = Discussion::where('discussion_id', $id)->update([
                    'subject' => $request->input('subject'),
                    'description' => $request->input('description'),
                    'priority' => $request->input('priority'),
                    'updated_by' => Auth::user()->id,
                    'updated_at' => date("Y-m-d H:i:s")
                ]);

                if ($update) {
                    $notification = "Discussion updated!";
                }
            }else{
                $discussion = new Discussion;
                $discussion->discussion_id = Str::uuid(4);
                $discussion->project_id = SubProject::where('subproject_id', Auth::user()->session_project)->value('project_id');
                $discussion->subproject_id = Auth::user()->session_project;
                $discussion->user_id = Auth::user()->user_id;
                $discussion->client_id = \globals::get_client_id();
                $discussion->subject = $request->input('subject');
                $discussion->description = $request->input('comment');
                $discussion->priority = $request->input('priority');
                $discussion->tag = $request->input('tag');
                $discussion->status = \globals::set_qna_status_unanswered();
                $discussion->created_by = Auth::user()->id;
                $discussion->created_at = date("Y-m-d H:i:s");

                if ($discussion->save()) {
                    $comment = new DiscussionComment;
                    $comment->discussion_id = $discussion->discussion_id;
                    $comment->project_id = $discussion->project_id;
                    $comment->subproject_id = $discussion->subproject_id;
                    $comment->user_id = $discussion->user_id;
                    $comment->client_id = $discussion->client_id;
                    $comment->parent = 0;
                    $comment->content = $request->input('comment');
                    $comment->fullname = Auth::user()->name;
                    $comment->created_by = Auth::user()->id;
                    $comment->created_at = date("Y-m-d H:i:s");
                    if($comment->save()){
                        $this->attach_file_discussion($comment->id, $comment->discussion_id, $comment->project_id, $comment->subproject_id, $comment->client_id,  $request);
                        $this->link_file_discussion($comment->id, $comment->discussion_id, $comment->project_id, $comment->subproject_id, $comment->client_id,  $request);
                        $this->send_email_user($comment->project_id, $comment->subproject_id, $comment->client_id, $comment->discussion_id, $request);
                        $notification = "Discussion created!";
                        $discussion_id = $discussion->discussion_id;
                        $results = [
                            'errcode' => 200,
                            'message' => "Discussion created!",
                            'link' => route('discussion.detail-discussion', $discussion_id)
                        ];
                    }
                }
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            $notification = "Error";
            $results = [
                'errcode' => 500,
                'message' => $e->getMessage(),
                'link' => null
            ];
        }

        Session::flash('notification', $notification);
        return response()->json($results);
        // return redirect(route('discussion.detail-discussion', $discussion_id))->with('notification', $notification);
    }

    function save_comment(Request $request) {
        $notification = "";
        try {
            \DB::beginTransaction();

            $id = base64_decode($request->input('id'));
            $getDiscussion = DiscussionComment::where('id', $id)->first();

            $comment = new DiscussionComment;
            $comment->discussion_id = $getDiscussion->discussion_id;
            if (Auth::user()->type == \globals::set_role_administrator()) {
                $comment->subproject_id = $getDiscussion->subproject_id;
                $comment->project_id = $getDiscussion->project_id;
            }else{
                $comment->subproject_id = Auth::user()->session_project;
                $comment->project_id = SubProject::where('subproject_id', Auth::user()->session_project)->value('project_id');
            }
            $comment->user_id = Auth::user()->user_id;
            $comment->client_id = \globals::get_client_id();
            $comment->parent = $id;
            $comment->content = $request->input('comment');
            $comment->fullname = Auth::user()->name;
            $comment->created_by = Auth::user()->id;
            $comment->created_at = date("Y-m-d H:i:s");
            if($comment->save()){
                $this->attach_file_discussion($comment->id, $comment->discussion_id, $comment->project_id, $comment->subproject_id, $comment->client_id,  $request);
                $this->link_file_discussion($comment->id, $comment->discussion_id, $comment->project_id, $comment->subproject_id, $comment->client_id,  $request);
                $this->send_email_user($comment->project_id, $comment->subproject_id, $comment->client_id, $comment->discussion_id, $request);
                $notification = "Discussion created!";
            }

            \DB::commit();
        } catch (\Exception $e) {
          \DB::rollback();
          Alert::error('Error', $e->getMessage());
          return back();
        }
        
        Session::flash('notification', $notification);
        return response()->json($notification);
    }

    function send_email_user($project_id, $subproject_id, $client_id, $discussion_id, $request){
        $discussion_creator = User::where('id', Auth::user()->id)->first();
        $receiver_email = AssignProject::where('subproject_id', $subproject_id)->where('client_id', $client_id)->get();
        if(count($receiver_email) > 0){
            foreach ($receiver_email as $key => $value) {
                if ($value->RefUser->email != Auth::user()->email) {
                    $set_subject = "";
                    if(!empty($request->input('subject'))){
                        $set_subject = $request->input('subject');
                    }else{
                        $set_subject = Discussion::where('discussion_id', $discussion_id)->where('client_id', $client_id)->value('subject');
                    }

                    $details = [
                        'discussion_creator' => $discussion_creator->name,
                        'receiver_name' => $value->RefUser->name,
                        'project_name' => $value->RefProject->project_name,
                        'subject' => $set_subject,
                        'comment' => $request->input('comment'),
                        'link' => route('discussion.detail-discussion', $discussion_id)
                    ];
    
                    \Mail::to($value->RefUser->email)->send(new \App\Mail\DiscussionUsers($details));
                } 
            }
        }
    }

    function delete_comment($id) {
        $notification = "";
        try {
            \DB::beginTransaction();

            $deleted = DiscussionComment::where('id', base64_decode($id))->update([
                'deleted' => 1
            ]);
            if($deleted){
                $notification = "Comment deleted!";
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            Alert::error('Error', $e->getMessage());
        }

        return back()->with('notification', $notification);
    }

    function delete_discussion($id) {
        $notification = "";
        try {
            \DB::beginTransaction();

            $deleted = Discussion::where('id', base64_decode($id))->update([
                'deleted' => 1
            ]);
            if($deleted){
                $notification = "Discussion deleted!";
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            Alert::error('Error', $e->getMessage());
        }

        return back()->with('notification', $notification);
    }

    public function import_questions(Request $request) {
        $request->validate([
            'upload_qna' => 'required|mimes:csv,txt'
        ]);

        $notification = "";
        $results = [];
        try {
            \DB::beginTransaction();

            $file = $request->file('upload_qna');
            
            $cvtArrayFile = $this->csvToArray($file);
            if (count($cvtArrayFile) > 0) {
                foreach ($cvtArrayFile as $files) {
                    $convertPriority = 0;
                    if (!empty($files['Priority']) && $files['Priority'] == "High") {
                        $convertPriority = 3;
                    }elseif(!empty($files['Priority']) && $files['Priority'] == "Medium"){
                        $convertPriority = 2;
                    }elseif(!empty($files['Priority']) && $files['Priority'] == "Low"){
                        $convertPriority = 1;
                    }
                    
                    $discussion = new Discussion;
                    $discussion->discussion_id = Str::uuid(4);
                    $discussion->subproject_id = Auth::user()->session_project;
                    $discussion->project_id = SubProject::where('subproject_id', Auth::user()->session_project)->value('project_id');
                    $discussion->user_id = Auth::user()->user_id;
                    $discussion->client_id = \globals::get_client_id();
                    $discussion->subject = $files['Subject'];
                    $discussion->description = $files['Comment'];
                    $discussion->priority = $convertPriority;
                    $discussion->status = \globals::set_qna_status_unanswered();
                    $discussion->created_by = Auth::user()->id;
                    $discussion->created_at = date("Y-m-d H:i:s");
                    if ($discussion->save()) {
                        $comment = new DiscussionComment;
                        $comment->discussion_id = $discussion->discussion_id;
                        $comment->project_id = $discussion->project_id;
                        $comment->user_id = $discussion->user_id;
                        $comment->client_id = $discussion->client_id;
                        $comment->parent = 0;
                        $comment->content = $files['Comment'];
                        $comment->fullname = Auth::user()->name;
                        $comment->created_by = Auth::user()->id;
                        $comment->created_at = date("Y-m-d H:i:s");
                        $comment->save();
                    }
                }

                $historyQNA = new HistoryImportDiscussion;
                $historyQNA->client_id = \globals::get_client_id();
                $historyQNA->project_id = Auth::user()->session_project;
                $historyQNA->user_id = Auth::user()->user_id;
                $historyQNA->file = $file->getClientOriginalName();
                $historyQNA->created_by = Auth::user()->id;
                if($historyQNA->save()){
                    $notification = "".count($cvtArrayFile)." questions submitted";
                    $results = [
                        'errcode' => 200,
                        'message' => "".count($cvtArrayFile)." questions submitted",
                    ];
                }
            }else{
                $notification = "data not found";
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            $results = [
                'errcode' => 500,
                'message' => $e->getMessage()
            ];
        }

        Session::flash('notification', $notification);
        return response()->json($results);
    }

    public function export_questions() {
        $filename = date('Y-m-d').'-discussion.xlsx';
        $report = DiscussionComment::select(
                    'discussion_comments.*', 'discussions.subject', 'discussions.priority', 'project.project_name',
                    'discussions.created_at as created_submitter', 'discussion_comments.created_at as created_comment',
                    'sub_project.subproject_name', 'discussions.user_id as submitter', 'discussion_comments.user_id as comment_by'
                )
                ->join('discussions', 'discussions.discussion_id', 'discussion_comments.discussion_id')
                ->join('sub_project', 'sub_project.subproject_id', 'discussion_comments.subproject_id')
                ->join('project', 'project.project_id', 'sub_project.project_id')
                ->where('discussion_comments.subproject_id', Auth::user()->session_project)
                ->get();
        
        return Excel::download(new ExportQuestions($report), $filename);
    }
    private function csvToArray($filename = '', $delimiter = ';'){
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    public function change_status_qna_closed(Request $request) {
        $notification = "";
        try {
            \DB::beginTransaction();

            $discussion_id = $request->input('discussion_id');
            $updated = Discussion::where('discussion_id', $discussion_id)->update([
                'status' => \globals::set_qna_status_closed(),
                'closed_date' => date('d-m-y H:i:s'),
                'closed_by' => Auth::user()->id
            ]);

            if($updated){
                $notification = "Question closed";
            }

            \DB::commit();
        } catch (\Exception $e) {
          \DB::rollback();
          Alert::error('Error', $e->getMessage());
          return back();
        }

        return back()->with('notification', $notification);
    }

    public function change_status_qna_open(Request $request) {
        $notification = "";
        try {
            \DB::beginTransaction();

            $discussion_id = $request->input('discussion_id');
            $updated = Discussion::where('discussion_id', $discussion_id)->update([
                'status' => \globals::set_qna_status_unanswered(),
                'closed_date' => null,
                'closed_by' => null
            ]);

            if($updated){
                $notification = "Question open";
            }

            \DB::commit();
        } catch (\Exception $e) {
          \DB::rollback();
          Alert::error('Error', $e->getMessage());
          return back();
        }

        return back()->with('notification', $notification);
    }

    private function attach_file_discussion($comment_id, $discussion_id, $project_id, $subproject_id, $client_id, $request){
        if($request->has('upload_doc')){
            if(count($request->file('upload_doc')) > 0){
                foreach($request->file('upload_doc') as $uploads){
                    $path = 'uploads/' . Client::where('client_email', Auth::user()->email)->value('client_id').'/'.$project_id.'/discussion'; 
                    Storage::makeDirectory($path, 0755, true);
                    // $results = Storage::disk('public')->put($path, $uploads, 'public');
                    $baseName = Str::random(8);
                    $results = $uploads->storeAs($path, $baseName);
                    if($results){
                        
                        $attach = new DiscussionAttachFile;
                        $attach->comment_id = $comment_id;
                        $attach->discussion_id = $discussion_id;
                        $attach->project_id = $project_id;
                        $attach->subproject_id = $subproject_id;
                        $attach->client_id = $client_id;
                        $attach->user_id = Auth::user()->user_id;
                        $attach->file_name = $uploads->getClientOriginalName();
                        $attach->basename = $baseName;
                        $attach->file_url = $path;
                        $attach->file_extension = $uploads->getClientOriginalExtension();
                        $attach->file_size = $uploads->getSize();
                        if($attach->save()){
                            $exist_folder = UploadFolder::where('project_id', $project_id)->where('client_id', $client_id)->where('name', 'Discussion')->first();
                            if (empty($exist_folder->id)) {
                                $folders = new UploadFolder;
                                $folders->index = 9999;
                                $folders->project_id = $project_id;
                                $folders->subproject_id = $subproject_id;
                                $folders->basename = $baseName;
                                $folders->name = "Discussion";
                                $folders->client_id = \globals::get_client_id();
                                $folders->status = 1;
                                $folders->uploaded_by = Auth::user()->user_id; 
                                $folders->save();
                            }
                        }
                    }
                }
            }
        }
    }

    private function link_file_discussion($comment_id, $discussion_id, $project_id, $subproject_id, $client_id, $request){
        if($request->input('link_doc') > 0){
            foreach($request->input('link_doc') as $files){
                $link_doc = new DiscussionLinkFile;
                $link_doc->comment_id = $comment_id;
                $link_doc->discussion_id = $discussion_id;
                $link_doc->project_id = $project_id;
                $link_doc->subproject_id = $subproject_id;
                $link_doc->client_id = $client_id;
                $link_doc->file_id = $files;
                $link_doc->file_name = UploadFile::where('id', $files)->pluck('name')->first();
                $link_doc->user_id = Auth::user()->user_id;
                $link_doc->created_by = Auth::user()->id;
                $link_doc->save();
            }
        }
    }
}