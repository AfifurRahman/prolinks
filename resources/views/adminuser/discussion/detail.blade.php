@extends('layouts.app_client')

@section('navigationbar')
@endsection

@section('notification')
    @if(session('notification'))
        <div class="notificationlayer">
            <div class="notification" id="notification">
                <image class="notificationicon" src="{{ url('template/images/icon_menu/checklist.png') }}"></image>
                <p class="notificationtext">{{ session('notification') }}</p>
            </div>
        </div>
    @endif
@endsection

@section('content')
    <style>
        .notificationlayer {
	        position: absolute;
	        width:100%;
	        height:50px;
	        z-index: 1;
	        pointer-events: none;
	    }

	    #notification {
	        background-color: #FFFFFF;
	        border: 2px solid #12B76A;
	        border-radius: 8px;
	        display: flex;
	        color: #232933;
	        margin: 50px auto;
	        text-align: center;
	        height: 48px;
	        position: absolute;
	        top: 0;
	        left: 50%;
	        transform: translateX(-50%);
	        transition: top 0.5s ease;    
	    }

	    .notificationicon {
	        width:20px;
	        height:20px;
	        margin-top:11px;
	        margin-left:15px;
	    }

	    .notificationtext{
	        margin-top:11px;
	        margin-left:8px;
	        margin-right:13px;
	        font-size:14px;
	    }

        .box-info {
            margin-left:60px;
        }

        .radius-button {
            border-radius: 8px;
        }

        .hidden-checkbox {
            display: none;
        }

        .box-info-qna{
            border-left: solid 1px #D0D5DD;
            position: fixed;
            width: 20%;
            height: 100%;
        }

        .borderless td {
            border: none !important;
        }

        .text-qna-closed {
            text-align: center;
            background: #FFFFFF;
            width: 70%;
            margin: 0 auto;
            color: #1D2939;
            font-size: 12px;
            font-weight: 600;
        }

        .box-time span {
            font-size:12px;
            color: #586474;
            font-weight: 400;
        }

        .box-time a {
            color: #1570EF;
            font-weight: 600;
            font-size:12px;
        }

        .profile-company-name {
            color: #586474 !important;
            font-size: 14px !important;
            font-weight: 400 !important;
        }

        .inbox-item-author {
            color: #1D2939 !important;
            font-weight: 600 !important;
            font-size: 14px !important;
        }

        .inbox-item-text {
            font-size: 14px !important;
            color: #1D2939 !important;
            font-weight: 400 !important;
            line-height: 20px;
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-left">
                <h3>
                    {{ $detail->subject }}
                    @if($detail->status == \globals::set_qna_status_unanswered())
                        <label class="label label-primary" style="background-color:#D1E9FF; color:#175CD3; border-radius:10px; font-size:14px;">Submitted</label>
                    @elseif($detail->status == \globals::set_qna_status_answered())
                        <label class="label label-success" style="background-color:#D1FADF; color:#3C914D; border-radius:10px; font-size:14px;">Answered</label>
                    @elseif($detail->status == \globals::set_qna_status_closed())
                        <label class="label label-inverse" style="background-color:#EDF0F2; color:#1D2939; border-radius:10px; font-size:14px;">Closed</label>
                    @endif
                </h3>
            </div>
            <div class="pull-right">
                @if(Auth::user()->type == \globals::set_role_collaborator() OR Auth::user()->type == \globals::set_role_administrator())
                    @if($detail->status == \globals::set_qna_status_unanswered() || $detail->status == \globals::set_qna_status_answered())
                        <a href="#modal-confirm-status-close" data-toggle="modal" class="btn btn-default" style="margin-top:5px;">Close questions</a>
                    @elseif($detail->status == \globals::set_qna_status_closed())
                        <a href="#modal-confirm-status-open" data-toggle="modal" class="btn btn-primary" style="margin-top:5px;">Open questions</a>
                    @endif
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-9">
            <div class="detail-qna">
                <div class="inbox-widget">
                    @foreach($detail->RefDiscussion as $comment)
                        @if(!empty($comment->RefUser->id))
                            <a href="#">
                                <div class="inbox-item">
                                    <div class="inbox-item-img">
                                        {!! \globals::get_user_avatar($comment->fullname, $comment->RefUser->avatar_color) !!}
                                        <!-- <img src="{{ url('template/images/avatar.png') }}" class="img-circle" alt=""> -->
                                    </div>
                                    <div class="box-info">
                                        <p class="inbox-item-author">{{ $comment->fullname }} · <span class="profile-company-name">{{ !empty($comment->RefClientUser->company) ? $comment->RefClient->company : '' }}</span></p>
                                        <p class="inbox-item-text">{{ $comment->content }}</p>
                                        <div class="box-file">
                                            @foreach($comment->RefDiscussionLinkFile as $link_file)
                                                @if(!empty($link_file->RefFile->id))
                                                    <div style="margin-bottom:5px;">
                                                        @php
                                                            $origin1 = $link_file->RefFile->directory;
                                                            $files1 = $link_file->RefFile->directory."/".$link_file->RefFile->basename;
                                                        @endphp
                                                        <a href="{{ route('adminuser.documents.downloadfile', [ base64_encode($origin1), base64_encode(basename($files1)) ] ) }}" class="btn btn-default radius-button">
                                                            <i class="fa fa-paperclip"></i> {{ $link_file->file_name }} <i class="fa fa-download"></i>
                                                        </a>
                                                    </div>
                                                @endif                                
                                            @endforeach

                                            @foreach($comment->RefDiscussionAttachFile as $attach_file)
                                                <div>
                                                    @php
                                                        $subproject = $attach_file->project_id."/".$attach_file->subproject_id;
                                                        $origin = 'uploads/'.$attach_file->client_id. '/'.$subproject.'/discussion';
                                                        $files = $attach_file->file_url."/".$attach_file->basename;
                                                    @endphp
                                                    <a href="{{ route('adminuser.documents.downloadfile', [ base64_encode($origin), base64_encode(basename($files)) ] ) }}" class="btn btn-default radius-button">
                                                    <i class="fa fa-file"></i> {{ $attach_file->file_name }} <i class="fa fa-download"></i>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="box-time">
                                            <span>{{ date('d M Y H:i', strtotime($comment->created_at)) }}</span>  
                                            @if(Auth::user()->id == $comment->created_by)
                                                · <a href="{{ route('discussion.delete-comment', base64_encode($comment->id)) }}" onclick="return confirm('are you sure delete this comment ?')">Delete</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endif
                    @endforeach

                    @if($detail->status == \globals::set_qna_status_unanswered() || $detail->status == \globals::set_qna_status_answered())
                        <div class="inbox-item">
                            <div class="box-info">
                                <form id="fileForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="input-group">
                                        <input type="hidden" name="id" value="{{ !empty($comment->id) ? base64_encode($comment->id) : '' }}">
                                        <textarea style="width:100%;" name="comment" id="comment" placeholder="Add an answer"></textarea>
                                        <span class="input-group-btn">
                                            <button id="actSubmitQNA" type="submit" class="btn btn-lg btn-primary" style="margin-top:-5px;">Submit</button>
                                        </span>
                                    </div>
                                    <div class="form-group" style="margin-top:5px;">
                                        <div style="float:left">
                                            <a href="#modal-link-file" data-toggle="modal" class="btn btn-default radius-button" style="color:#1570EF;"><i class="fa fa-paperclip"></i> Select from dataroom</a>
                                        </div>
                                        <div style="float:left; margin-left:10px;">
                                            <input type="file" class="btn btn-default radius-button" style="color:#1570EF;" name="upload_doc[]" id="upload_doc" multiple />
                                        </div> <div style="clear:both;"></div>
                                        <div id="result-link-file" style="margin-top:10px;"></div>
                                        <div id="result-upload-file"></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="text-qna-closed">Closed by {{ !empty($detail->RefClosedUser->name) ? $detail->RefClosedUser->name : '' }} on {{ !empty($detail->closed_date) ? date('d M Y H:i', strtotime($detail->closed_date)) : '' }}</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="box-info-qna">
                <table class="table borderless">
                    <tr>
                        <td width="100">ID</td>
                        <td>{{ $detail->id }}</td>
                    </tr>
                    <tr>
                        <td>Priority</td>
                        <td>{!! \globals::label_qna_priority($detail->priority) !!}</td>
                    </tr>
                    <tr>
                        <td>Project</td>
                        <td>{{ !empty($detail->RefProject->project_name) ? $detail->RefProject->project_name : '' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    @include('adminuser.discussion.modal_link_file')
    @include('adminuser.discussion.modal_close_questions')
    @include('adminuser.discussion.modal_open_questions')
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $(".tableLinksFiles").dataTable({
                "bPaginate": false,
                "bInfo": true,
                "bSort": false,
            });
        });

        function hideNotification() {
        setTimeout(function() {
            $('#notification').fadeOut();
            }, 2000);
        };

        hideNotification();

        function getLinkDoc() {
            $("#modal-link-file").modal('hide');
            var link_document = $('[name="link_document"]');
            var res = "";
            $.each(link_document, function(i) {
                var $this = $(this);
                if($this.is(":checked")) {
                    res += "<div class='linkItem"+i+"' style='margin-bottom:3px;'>"
                        res += "<div class='btn-group'>"
                            res += "<a class='btn btn-default radius-button'><i class='fa fa-paperclip'></i> <input type='checkbox' name='link_doc[]' class='hidden-checkbox' value='"+$this.val()+"' checked>"+$this.data('filename')+"</a>"
                            res += "<a class='btn btn-default' title='remove file' href='javascript:void(0)' onclick='removeItem("+i+")'><i class='fa fa-times'></i></a>"
                        res += "</div>"
                    res += "</div>"
                }
            }); 
            
            $("#result-link-file").html(res);
        }

        $("#upload_doc").change(function(){
            var names = [];
            for (var i = 0; i < $(this).get(0).files.length; ++i) {
                names += "<div class='uploadItem"+i+"' style='margin-bottom:3px;'>"
                    names += "<div class='btn-group'>"
                        names += "<a class='btn btn-default radius-button'><i class='fa fa-upload'></i> "+$(this).get(0).files[i].name+"</a>"
                        names += "<a class='btn btn-default' href='javascript:void(0)' onclick='removeUploadItem("+i+")'><i class='fa fa-times'></i></a>"
                    names += "</div>"
                names += "</div>"
            }
            
            $("#result-upload-file").append(names);
        })

        function removeItem(idx) {
            var r = confirm("are you sure remove this item ?");
            if (r == true) {
                $(".linkItem"+idx).remove();
            }
        }

        function removeUploadItem(idx) {
            var r = confirm("are you sure remove this item ?");
            if (r == true) {
                $(".uploadItem"+idx).remove();
            }
        }

        $('#fileForm').submit(function(e){
            e.preventDefault();
            var formData = new FormData(this);
            var comment = $("#comment").val();
            if(comment == ""){
                alert("comment required");
                $("#comment").focus();
            }else{
                $.ajax({
                    url: "{{ route('comment.save-comment') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function(){
                        $("#actSubmitQNA").prop('disabled', true);
                        $("#actSubmitQNA").html("loading..");
                    },
                    success: function(response){
                        console.log(response);
                        location.reload();
                    },
                    error: function(xhr, status, error){
                        alert(xhr.responseText);
                    }
                });
            }
        });
    </script>
@endpush