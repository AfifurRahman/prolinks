<table style="border:solid 1px #000000;">
	<thead>
		<tr>
			<th style="text-align:left; font-size:17px">
				<h3>Project Rome : Q&A All Questions</h3>
			</th>
		</tr>
		<tr>
			<th style="text-align:center; font-size:15px">
				<h3>As of: {{ now()->format('M d, Y h:i A T') }}</h3>
			</th>
		</tr>
		<tr>
			<th>&nbsp;</th>
		</tr>
		<tr>
			<th>&nbsp;</th>
		</tr>
		<tr>
			<th>Question ID</th>
			<th>Question Title</th>
			<th>Priority</th>
			<th>Current Status</th>
            <th>Submitted Date</th>
			<th>Submitted By User</th>
            <th>Last Updated On</th>
            <th>Question</th>
			<th>Latest Answer</th>
			<th>Total Number of Followups</th>
			<th>Total Number of Answers</th>
			<th>Related Document(s)</th>
		</tr>
	</thead>
	<tbody>
		@foreach($report as $key => $value)
            <tr>
				<td style="width:120px;">{{ $loop->iteration }}</td>
				<td style="width:120px;">{{ $value->subject }}</td>
				<td style="width:120px;">{!! \globals::get_username($value->submitter) !!}</td>
				<td style="width:120px;">{!! \globals::label_qna_status($value->status) !!}</td>
				<td style="width:120px;">{!! \globals::label_qna_priority($value->priority) !!}</td>
                <td style="width:120px;">{{ $value->created_submitter }}</td>
				<td style="width:120px;">{{ $value->content }}</td>
                <td style="width:120px;">{!! \globals::get_username($value->comment_by) !!}</td>
				<td style="width:120px;">{{ $value->created_comment }}</td>
			</tr>
        @endforeach
	</tbody>
</table>