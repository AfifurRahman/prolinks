<table class="tableGlobal">
    <thead>
        <tr>
            <th></th>
            <th>ID</th>
            <th>Subject</th>
            <th>Submitter</th>
            <th>Tag</th>
            <th>Priority</th>
            <th>Submitted at</th>
        </tr>
    </thead>
    <tbody>
        @foreach($answered as $key => $qna)
            <tr>
                <td>
                    <input type="checkbox" />
                </td>
                <td>{{ $qna->id }}</td>
                <td><a href="{{ route('discussion.detail-discussion', $qna->discussion_id) }}">{{ $qna->subject }}</a></td>
                <td>{!! \globals::get_username($qna->user_id) !!}</td>
                <td>-</td>
                <td>{!! \globals::label_qna_priority($qna->priority) !!}</td>
                <td>{!! date('d M Y H:i', strtotime($qna->created_at)) !!}</td>
            </tr>
        @endforeach
    </tbody>
</table>