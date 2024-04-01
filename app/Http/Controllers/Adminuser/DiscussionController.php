<?php

namespace App\Http\Controllers\Adminuser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Models\Discussion;
use App\Models\DiscussionComment;
use App\Models\DiscussionAttachFile;
use App\Models\Client;
use App\Models\ClientUser;
use Auth;
use Session;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DiscussionController extends Controller
{
    function index() {
        $all_questions = Discussion::orderBy('id', 'DESC')->where('client_id', \globals::get_client_id())->get();
        $unanswered = Discussion::orderBy('id', 'DESC')->where('client_id', \globals::get_client_id())->where('status', \globals::set_qna_status_unanswered())->get();
        $answered = Discussion::orderBy('id', 'DESC')->where('client_id', \globals::get_client_id())->where('status', \globals::set_qna_status_answered())->get();
        $closed = Discussion::orderBy('id', 'DESC')->where('client_id', \globals::get_client_id())->where('status', \globals::set_qna_status_closed())->get();
        
        return view('adminuser.discussion.index', compact('all_questions', 'unanswered', 'answered', 'closed'));
    }

    function detail($discussion_id){
        $details = Discussion::where('discussion_id', $discussion_id)->where('client_id', \globals::get_client_id())->first();
        return view('adminuser.discussion.detail', compact('discussion_id', 'details'));
    }

    function save_discussion(Request $request){
        try {
            \DB::beginTransaction();

            $id = $request->input('discussion_id');
            $discussion_id = $id;
            if ($id != NULL) {
                $update = Discussion::where('discussion_id', $id)->update([
                    'project_id' => Session::get('project_id'),
                    'user_id' => Auth::user()->user_id,
                    'client_id' => \globals::get_client_id(),
                    'subject' => $request->input('subject'),
                    'description' => $request->input('description'),
                    'priority' => $request->input('priority'),
                    'tag' => $request->input('tag'),
                    'updated_by' => Auth::user()->id,
                    'updated_at' => date("Y-m-d H:i:s")
                ]);

                if ($update) {
                    $notification = "Discussion updated!";
                }
            }else{
                $discussion = new Discussion;
                $discussion->discussion_id = Str::uuid(4);
                $discussion->project_id = Session::get('project_id');
                $discussion->user_id = Auth::user()->user_id;
                $discussion->client_id = \globals::get_client_id();
                $discussion->subject = $request->input('subject');
                $discussion->description = $request->input('description');
                $discussion->priority = $request->input('priority');
                $discussion->tag = $request->input('tag');
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
                    $comment->content = $request->input('description');
                    $comment->fullname = Auth::user()->name;
                    $comment->created_by = Auth::user()->id;
                    $comment->created_at = date("Y-m-d H:i:s");
                    if($comment->save()){
                        $this->attach_file_discussion($comment->id, $comment->discussion_id, $comment->project_id, $comment->client_id,  $request);
                        $notification = "Discussion created!";
                        $discussion_id = $discussion->discussion_id;
                    }
                }
            }

            \DB::commit();
        } catch (\Exception $e) {
          \DB::rollback();
          Alert::error('Error', $e->getMessage());
          return back();
        }

        return redirect(route('discussion.detail-discussion', $discussion_id))->with('notification', $notification);
    }

    public function get_comment($discussion_id){
        $comments = DiscussionComment::where('discussion_id', $discussion_id)->get();

        $commentsArray = [];
        foreach ($comments as $comment) {
            $commentsArray[] = [
                "id" => $comment->id,
                "parent" => $comment->parent,
                "created" => $comment->created_at,
                "modified" => $comment->updated_at,
                "content" => $comment->content,
                "attachments" => $this->get_attachments($comment->id),
                "pings" => [],
                "creator" => Auth::user()->id,
                "fullname" => $comment->fullname,
                "profile_picture_url" => "https://viima-app.s3.amazonaws.com/media/public/defaults/user-icon.png",
                "created_by_admin" => false,
                "created_by_current_user" => false,
                "is_new" => false
            ];
        }

        return response()->json($commentsArray);
    }

    private function get_attachments($comment_id){
        $attachments = DiscussionAttachFile::select('id', 'file_extension as mime_type', 'file_name as file')->where("comment_id", $comment_id)->get();
        return $attachments;
    }

    public function post_comment(Request $request, $discussion_id){
        
    }

    public function delete_comment(Request $request, $discussion_id){
        
    }

    private function attach_file_discussion($comment_id, $discussion_id, $project_id, $client_id, $request){
        if($request->has('attach_file')){
            $path  = "disscusion/".Session::get("project_id")."/attach_file/";
            $files = $request->file('attach_file');
            $fileName = time().'.'.$files->getClientOriginalExtension();

            $results = Storage::disk('public')->put($path, $files, 'public');

            if($results){
                $attach = new DiscussionAttachFile;
                $attach->comment_id = $comment_id;
                $attach->discussion_id = $discussion_id;
                $attach->project_id = $project_id;
                $attach->client_id = $client_id;
                $attach->user_id = Auth::user()->id;
                $attach->file_name = $fileName;
                $attach->file_url = $path;
                $attach->file_extension = $files->getClientOriginalExtension();
                $attach->file_size = $files->getSize();
                
                $attach->save();
            }
        }
    }
}