<?php

namespace App\Http\Controllers\Adminuser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Models\Discussion;
use App\Models\DiscussionComment;
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
        $questions = Discussion::orderBy('id', 'DESC')->get();
        return view('adminuser.discussion.index', compact('questions'));
    }

    function detail($discussion_id){
        return view('adminuser.discussion.detail');
    }

    function save_discussion(Request $request){
        try {
            \DB::beginTransaction();

            $id = $request->input('id');

            if ($id != NULL) {
                $update = Company::where('id', $id)->update([
                    'discussion_id' => Str::uuid(4),
                    'project_id' => Session::get('project_id'),
                    'user_id' => Auth::user()->user_id,
                    'subject' => $request->input('subject'),
                    'description' => $request->input('description'),
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
                $discussion->subject = $request->input('subject');
                $discussion->description = $request->input('description');
                $discussion->created_by = Auth::user()->id;
                $discussion->created_at = date("Y-m-d H:i:s");

                if ($discussion->save()) {
                    $comment = new DiscussionComment;
                    $comment->discussion_id = $discussion->discussion_id;
                    $comment->project_id = Session::get('project_id');
                    $comment->user_id = Auth::user()->user_id;
                    $comment->parent = 0;
                    $comment->content = $request->input('description');
                    $comment->fullname = Auth::user()->name;
                    if($comment->save()){
                        $this->attach_file_discussion($comment->id, $request);
                        $notification = "Discussion created!";
                    }
                }
            }

            \DB::commit();
      } catch (\Exception $e) {
          \DB::rollback();
          Alert::error('Error', $e->getMessage());
          return back();
      }

      return back()->with('notification', $notification);
    }

    private function attach_file_discussion($comment_id, $request){
        if($request->has('attach_file')){
            $path  = "disscusion/".Session::get("project_id")."/attach_file/";
            $files = $request->file('attach_file');
            $fileName = time().'.'.$files->getClientOriginalExtension();

            $results = Storage::disk('public')->put($path, $files, 'public');

            if($results){
                DiscussionComment::where('id', $comment_id)->update([
                    'file_name' => $fileName,
                    'file_mime_type' => $files->getClientOriginalExtension()
                ]);
            }
        }
    }
}