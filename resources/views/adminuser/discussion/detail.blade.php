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
    </style>
    <div id="comments-container"></div>
@endsection

@push('scripts')
    <script type="text/javascript">
        function hideNotification() {
        setTimeout(function() {
            $('#notification').fadeOut();
            }, 2000);
        };

        hideNotification();

        var saveComment = function(data) {
            // Convert pings to human readable format
            $(Object.keys(data.pings)).each(function(index, userId) {
                var fullname = data.pings[userId];
                var pingText = '@' + fullname;
                data.content = data.content.replace(new RegExp('@' + userId, 'g'), pingText);
            });

            return data;
        }

        $('#comments-container').comments({
            profilePictureURL: '{{ url("template/images/avatar.png") }}',
            currentUserId: 1,
            roundProfilePictures: true,
            textareaRows: 1,
            enableAttachments: true,
            enableUpvoting: false,
            searchUsers: function(term, success, error) {
                setTimeout(function() {
                    success(usersArray.filter(function(user) {
                        var containsSearchTerm = user.fullname.toLowerCase().indexOf(term.toLowerCase()) != -1;
                        var isNotSelf = user.id != 1;
                        return containsSearchTerm && isNotSelf;
                    }));
                }, 500);
            },
            getComments: function(success, error) {
                setTimeout(function() {
                    $.get("{{ route('discussion.get-comment', $discussion_id) }}" ,function(response){
                        success(response);
                    },'json');
                }, 500);
            },
            postComment: function(commentJSON, success, error) {
                setTimeout(function() {
                    $.ajax({
                        type: 'post',
                        url: "{{ route('discussion.post-comment', $discussion_id) }}",
                        data: commentJSON,
                        success: function(comment) {
                            comment = JSON.parse(comment);
                            success(comment)
                        },
                        error: error
                    });
                }, 500);
            },
            putComment: function(data, success, error) {
                setTimeout(function() {
                    success(saveComment(data));
                }, 500);
            },
            deleteComment: function(data, success, error) {
                setTimeout(function() {
                    success();
                }, 500);
            },
            validateAttachments: function(attachments, callback) {
                setTimeout(function() {
                    callback(attachments);
                }, 500);
            },
        });
    </script>
@endpush