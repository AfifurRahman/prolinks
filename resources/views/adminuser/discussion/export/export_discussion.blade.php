<table style="border:solid 1px #000000;">
	<thead>
		<tr>
			<th style="text-align:left; font-size:17px;font-weight:bold;" colspan="12">
				<h3>{{'Project Room : Q&A All Questions' }}</h3>
			<th>
		</tr>
		<tr>
			<th style="text-align:left; font-size:17px;font-weight:bold;" colspan="12">
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
			<th style="width:120px;">Question ID</th>
			<th style="width:120px;">Question Title</th>
			<th style="width:120px;">Priority</th>
			<th style="width:120px;">Current Status</th>
            <th style="width:120px;">Submitted Date</th>
			<th style="width:120px;">Submitted By User</th>
            <th style="width:120px;">Last Updated On</th>
            <th style="width:120px;">{{ 'Question / Answer' }}</th>
			<th style="width:120px;">Latest Answer</th>
			<th style="width:120px;">Related Document(s)</th>
		</tr>
	</thead>
	<tbody>
		@foreach($report as $key => $value)
            <tr>
				<td style="width:120px;">{{ sprintf("ID%05d", DB::table('discussions')->where('discussion_id', $value->discussion_id)->value('id')) }}</td>
				<td style="width:120px;">{{ $value->subject }}</td>
				<td style="width:120px;">{!! \globals::label_qna_priority($value->priority) !!}</td>
				<td style="width:120px;">{!! \globals::label_qna_status($value->status) !!}</td>
				<td style="width:120px;">{{ $value->created_at }}</td>
				<td style="width:120px;">{{ DB::table('users')->where('user_id', $value->user_id)->value('name') }}</td>
				<td style="width:120px;">{{ $value->updated_at }}</td>
				<td style="width:120px;">{{ $value->content }}</td>
				<td style="width:120px;">{{ DB::table('discussion_comments')->where('id', DB::table('discussion_comments')->where('discussion_id', $value->discussion_id)->max('id'))->value('content') }}</td>
				<td style="width:120px;">{{ DB::table('discussion_attach_files')->where('comment_id',$value->id)->value('file_name') }}</td>
			</tr>
        @endforeach
	</tbody>

</table>