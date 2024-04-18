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
    </style>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-left">
                <h3>
                    {{ $detail->subject }}
                    @if($detail->status == \globals::set_qna_status_unanswered())
                        <label class="label label-primary">Open</label>
                    @elseif($detail->status == \globals::set_qna_status_closed())
                        <label class="label label-inverse">Question closed</label>
                    @endif
                </h3>
            </div>
            <div class="pull-right">
                @if($detail->status == \globals::set_qna_status_unanswered())
                    <a href="#modal-confirm-status-close" data-toggle="modal" class="btn btn-default" style="margin-top:5px;">Close questions</a>
                @elseif($detail->status == \globals::set_qna_status_closed())
                    <a href="#modal-confirm-status-open" data-toggle="modal" class="btn btn-primary" style="margin-top:5px;">Open questions</a>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-9">
            <div class="detail-qna">
                <div class="inbox-widget">
                    @foreach($detail->RefDiscussion as $comment)
                        <a href="#">
                            <div class="inbox-item">
                                <div class="inbox-item-img">
                                    <img src="{{ url('template/images/avatar.png') }}" class="img-circle" alt="">
                                </div>
                                <div class="box-info">
                                    <p class="inbox-item-author">{{ $comment->fullname }}</p>
                                    <p class="inbox-item-text">{{ $comment->content }}</p>
                                    <div class="box-file">
                                        @foreach($comment->RefDiscussionLinkFile as $link_file)
                                            <div style="margin-bottom:5px;">
                                                <a href="#" class="btn btn-default radius-button">
                                                    <i class="fa fa-paperclip"></i> {{ $link_file->file_name }} <i class="fa fa-download"></i>
                                                </a>
                                            </div>                                
                                        @endforeach

                                        @foreach($comment->RefDiscussionAttachFile as $attach_file)
                                            <div>
                                                <a href="#" class="btn btn-default radius-button">
                                                <i class="fa fa-file"></i> {{ $attach_file->file_name }} <i class="fa fa-download"></i>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach

                    @if($detail->status == \globals::set_qna_status_unanswered())
                        <div class="inbox-item">
                            <div class="box-info">
                                <form id="fileForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="input-group">
                                        <input type="hidden" name="id" value="{{ base64_encode($comment->id) }}">
                                        <input type="text" class="form-control" name="comment" id="comment" placeholder="Add an answer" />
                                        <span class="input-group-btn">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </span>
                                    </div>
                                    <div class="form-group" style="margin-top:5px;">
                                        <div style="float:left">
                                            <a href="#modal-link-file" data-toggle="modal" class="btn btn-default radius-button" style="color:#1570EF;"><i class="fa fa-paperclip"></i> Select from dataroom</a>
                                        </div>
                                        <div style="float:left; margin-left:10px;">
                                            <input type="file" class="btn btn-default radius-button" style="color:#1570EF;" name="upload_doc[]" id="upload_doc" multiple />
                                        </div> <div style="clear:both;"></div>
                                        <div id="result-link-file"></div>
                                        <div id="result-upload-file"></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="text-qna-closed">Closed by Christin Purnama Sari on 12 April 2024, 13:54</div>
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
                        <td>{{ $detail->RefProject->project_name }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div id="modal-link-file" class="modal fade" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="custom-modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <div style="float: left;">
                            <img src="{{ url('template/images/data-company.png') }}" width="24" height="24">
                        </div>
                        <div style="float: left; margin-left: 10px;">
                            <h4 class="modal-title" id="titleModal">
                                Select from dataroom
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <table class="table table-hover">
                        @foreach($file as $files)
                            <tr>
                                <td><input type="checkbox" value="{{ $files->id }}" data-filename="{{ $files->name }}" name="link_document" id="link_document" /> {{ $files->name }} </td>
                            </tr>
                        @endforeach
                    </table>
                    
                    <button type="button" class="btn btn-primary" onclick="getLinkDoc()">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-confirm-status-close" class="modal fade" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="custom-modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="titleModal">
                            Close Question ?
                        </h4>
                    </div>
                </div>
                <div class="modal-body">
                    <form action="{{ route('discussion.change-status-qna-closed', $detail->discussion_id) }}" method="POST">
                        @csrf
                        <label>Once closed, no follow-up questions can be asked. Do you want to proceed?</label>
                        <div class="pull-right">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Yes, close</button>
                        </div>
                    </form>
                    <div style="clear:both;"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-confirm-status-open" class="modal fade" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" keyboard="false" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="custom-modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="titleModal">
                            Open Question ?
                        </h4>
                    </div>
                </div>
                <div class="modal-body">
                    <form action="{{ route('discussion.change-status-qna-open', $detail->discussion_id) }}" method="POST">
                        @csrf
                        <label>Once open, follow-up questions can be asked. Do you want to proceed?</label>
                        <div class="pull-right">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Yes, open</button>
                        </div>
                    </form>
                    <div style="clear:both;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
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
                console.log($this.data('filename'));
                console.log("+++++++++++++");
                // check if the checkbox is checked
                if($this.is(":checked")) {
                    res += "<div class='linkItem"+i+"'>"
                        res += "<label class='label label-default'><i class='fa fa-paperclip'></i> <input type='checkbox' name='link_doc[]' class='hidden-checkbox' value='"+$this.val()+"' checked>"+$this.data('filename')+"</label>"
                        res += "<a href='javascript:void(0)' onclick='removeItem("+i+")'><i class='fa fa-times'></i></a>"
                    res += "</div>"
                }
            }); 
            
            $("#result-link-file").html(res);
        }

        $("#upload_doc").change(function(){
            var names = [];
            for (var i = 0; i < $(this).get(0).files.length; ++i) {
                names += "<div class='uploadItem"+i+"'>"
                    names += "<label class='label label-inverse'><i class='fa fa-upload'></i> "+$(this).get(0).files[i].name+"</label>"
                    names += "<a href='javascript:void(0)' onclick='removeUploadItem("+i+")'><i class='fa fa-times'></i></a>"
                names += "</div>"
            }
            $("#result-upload-file").html(names);
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
                $('#upload_doc').val('');
            }
        }

        $('#fileForm').submit(function(e){
            e.preventDefault();
            var formData = new FormData(this);
            console.log(formData);
            $.ajax({
                url: "{{ route('comment.save-comment') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response){
                    console.log(response);
                    location.reload();
                },
                error: function(xhr, status, error){
                    alert(xhr.responseText);
                }
            });
        });
    </script>
@endpush