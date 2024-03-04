<table class="tableGlobal">
    <thead>
        <tr>
            <th></th>
            <th>Number Questions</th>
            <th>Subject</th>
            <th>Total users</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($questions as $key => $qna)
            <tr>
                <td>
                    <input type="checkbox" />
                </td>
                <td><a href="{{ route('discussion.detail-discussion', $qna->discussion_id) }}">500089</a></td>
                <td>{{ $qna->subject }}</td>
                <td>24</td>
                <td>{!! \globals::label_status_discussion($qna->status) !!}</td>
            </tr>
        @endforeach
    </tbody>
</table>