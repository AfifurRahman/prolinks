<table class="tableGlobal">
    <thead>
        <tr>
            <th>ID</th>
            <th>Subject</th>
            <th>Submitter</th>
            <th>Status</th>
            <th>Submitted at</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($list_questions as $key => $qna)
            <tr>
                <td width="50">{{ $qna->id }}</td>
                <td width="400">
                    @if($qna->priority == \globals::set_qna_priority_high())
                        <img src="{{ url('template/images/priority_high.png') }}" width="24" height="24" />
                    @elseif($qna->priority == \globals::set_qna_priority_medium())
                        <img src="{{ url('template/images/priority_medium.png') }}" width="24" height="24" />
                    @elseif($qna->priority == \globals::set_qna_priority_low())
                        <img src="{{ url('template/images/priority_low.png') }}" width="24" height="24" />
                    @endif
                    <a href="{{ route('discussion.detail-discussion', $qna->discussion_id) }}">{{ $qna->subject }}</a>
                </td>
                <td width="150">{!! \globals::get_user_avatar_small($qna->RefUser->user_id, $qna->RefUser->avatar_color) !!} &nbsp; {!! !empty($qna->RefUser->name) ? $qna->RefUser->name : '' !!}</td>
                <td width="100">{!! \globals::label_qna_status($qna->status) !!}</td>
                <td width="150">{!! date('d M Y H:i', strtotime($qna->created_at)) !!}</td>
                <td>
                    <div class="dropdown">
                        <button class="button_ico dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-top pull-right">
                            <li><a href="{{ route('discussion.detail-discussion', $qna->discussion_id) }}"></i>View Detail</a></li>
                            @if($qna->status == \globals::set_qna_status_unanswered() || $qna->status == \globals::set_qna_status_answered())
                                @if ($qna->user_id == Auth::user()->user_id) 
                                    <li><a href="#modal-confirm-status-close" data-discussionid="{{ $qna->discussion_id }}" onclick="getDiscussionID(this)" data-toggle="modal">Close question</a></li>
                                @endif
                            @elseif($qna->status == \globals::set_qna_status_closed())
                                @if ($qna->user_id == Auth::user()->user_id)
                                    <li><a href="#modal-confirm-status-open" data-discussionid="{{ $qna->discussion_id }}" onclick="getDiscussionID(this)" data-toggle="modal">Open question</a></li>
                                @endif
                            @endif

                            @if(Auth::user()->type == \globals::set_role_collaborator() || Auth::user()->type == \globals::set_role_client())
                                @if(Auth::user()->user_id == $qna->user_id)
                                    <li><a href="#modal-remove-questions" data-toggle="modal" data-url="{{ route('discussion.delete-discussion', $qna->discussion_id) }}" onclick="getUrlDeleteQna(this)" style="color:#D92D20;"></i>Remove question</a></li>
                                @endif
                            @endif
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>