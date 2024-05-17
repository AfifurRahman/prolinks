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
    <style type="text/css">
        .tableGlobal td {
			vertical-align: middle;
		}

		.tableGlobal{
		    border-collapse: separate;
		    border:1px solid #D0D5DD;
		    border-radius: 7px;
		    width:100%
		}

		.tableGlobal th {
		    padding: 10px 0px 10px 10px;
		    border-bottom:1px solid #D0D5DD;
		    font-size:14px;
		    font-weight:600;
		}

		.tableGlobal td  {
		    padding: 8px 0px 8px 10px;
		    border-bottom:1px solid #D0D5DD;
		    font-size:13.5px;
		    color:black;
		}

		.tableGlobal tbody tr:last-child td{
		    border-bottom: none;
		}

		.tableGlobal tbody tr:hover {
		    background-color: #f0f0f0;
		}

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

        .modal-content {
            padding: 0px !important;
            -webkit-border-radius: 0px !important;
		    -moz-border-radius: 0px !important;
		    border-radius: 10px !important; 
        }

        .modal-body {
            padding: 25px !important;
        }

        .custom-modal-header {
            padding: 5px;
            width: 95%;
            margin: 0 auto;
            margin-top: 13px;
        }

        .custom-form input {
            border-radius: 7px;
        }

        .custom-form select {
            border-radius: 7px;
        }

        .nav-custom li > a {
            text-transform: capitalize;
        }

        .hidden-checkbox {
            display: none;
        }

        .button_ico{
            border:none;
            background:transparent;
            margin-right:10px;

        }
    </style>

    <div class="pull-left">
		<h3 style="color:black;font-size:28px;">Questions and answers</h3>
	</div>
	<div class="pull-right" style="margin-bottom: 24px; margin-top:5px;">
        <a href="{{ route('discussion.export-questions') }}" class="btn btn-md btn-default" style="border-radius: 9px; color:#1570EF; font-weight:bold;"> Export All</a>
        @if(Auth::user()->type == \globals::set_role_collaborator() OR Auth::user()->type == \globals::set_role_client())
            <a href="#modal-import-questions" data-toggle="modal" class="btn btn-md btn-default" style="border-radius: 9px; color:#1570EF; font-weight:bold;">Import Questions</a>
            <a href="#modal-add-discussion" data-toggle="modal" class="btn btn-md btn-primary" style="border-radius: 9px;">Ask a questions</a>
        @endif
    </div><div style="clear: both;"></div>
    @if(count($all_questions) > 0 || count($unanswered) > 0 || count($answered) > 0 || count($closed) > 0)
        <div>
            <ul class="nav nav-tabs tabs-bordered nav-custom">
                <li class="active">
                    <a href="#all" data-toggle="tab" aria-expanded="true">All</a>
                </li>
                <li class="">
                    <a href="#unanswered" data-toggle="tab" aria-expanded="false">Unanswered</a>
                </li>
                <li class="">
                    <a href="#answered" data-toggle="tab" aria-expanded="false">Answered</a>
                </li>
                <li class="">
                    <a href="#closed" data-toggle="tab" aria-expanded="false">Closed</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="all">
                    @include('adminuser.discussion.tabs.list_questions', ['list_questions' => $all_questions])
                </div>
                <div class="tab-pane" id="unanswered">
                    @include('adminuser.discussion.tabs.list_questions', ['list_questions' => $unanswered])
                </div>
                <div class="tab-pane" id="answered">
                    @include('adminuser.discussion.tabs.list_questions', ['list_questions' => $answered])
                </div>
                <div class="tab-pane" id="closed">
                    @include('adminuser.discussion.tabs.list_questions', ['list_questions' => $closed])
                </div>
            </div>
        </div>
    @else
		<div class="card-box">
			<center>
				<img src="{{ url('template/images/empty_qna.png') }}" width="300" />
			</center>    
		</div>
	@endif
    @include('adminuser.discussion.create_discussion')
    @include('adminuser.discussion.modal_close_questions')
    @include('adminuser.discussion.modal_open_questions')
    @include('adminuser.discussion.modal_import_questions')
    @include('adminuser.discussion.modal_remove_questions')
@endsection
@push('scripts')
	<script type="text/javascript">
        function hideNotification() {
        setTimeout(function() {
            $('#notification').fadeOut();
            }, 2000);
        };

        hideNotification();

        $(document).ready(function () {
            $('.tableGlobal').dataTable({
                "bPaginate": true,
                "bInfo": true,
                "bSort": true,
                "dom": 'rtip',
                "stripeClasses": false,
            });

            $(".tableLinksFiles").dataTable({
                "bPaginate": false,
                "bInfo": true,
                "bSort": false,
            });
        });

        function getLinkDoc() {
            $("#modal-link-file").modal('hide');
            var link_document = $('[name="link_document"]');
            var res = "";
            $.each(link_document, function(i) {
                var $this = $(this);
                // check if the checkbox is checked
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
                        names += "<a class='btn btn-default' title='remove file' href='javascript:void(0)' onclick='removeUploadItem("+i+")'><i class='fa fa-times'></i></a>"
                    names += "</div>"
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
                url: "{{ route('discussion.save-discussion') }}",
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
                    if(response.errcode == 200){
                        window.location.href = response.link;
                    }else{
                        location.reload();
                    }
                    
                },
                error: function(xhr, status, error){
                    alert(xhr.responseText);
                }
            });
        });

        $('#form-import').submit(function(e){
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: "{{ route('discussion.import-questions') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function(){
                    $("#actSubmitImport").prop('disabled', true);
                    $("#actSubmitImport").html("loading..");
                },
                success: function(response){
                    if(response.errcode == 200){
                        location.reload();
                    }else{
                        alert("error");
                    }
                    
                },
                error: function(xhr, status, error){
                    alert(xhr.responseText);
                }
            });
        });

        function getDiscussionID(element) {
            var discussion_id = $(element).data('discussionid');
            $("#discussion_id").val(discussion_id);
        }

        function getUrlDeleteQna(element) {
            var url = $(element).data('url');
            $("#get_url_delete_qna").val(url);
        }

        function actDeleteQna() {
            var getUrlDelete = $("#get_url_delete_qna").val();
            if (getUrlDelete != 'undefined') {
                window.location.href = getUrlDelete;
            }
        }
    </script>
@endpush