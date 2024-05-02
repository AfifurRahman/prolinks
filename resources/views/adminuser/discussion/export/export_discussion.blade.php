<table style="border:solid 1px #000000;">
	<thead>
		<tr>
			<th colspan="9" style="text-align:center; font-size:17px">
				<h3>Report Discussion</h3>
			</th>
		</tr>
		<tr>
			<th colspan="9" style="text-align:center; font-size:15px">
				<h3>{{ !empty($report[0]->project_name) ? $report[0]->project_name : '' }} ( {{ !empty($report[0]->subproject_name) ? $report[0]->subproject_name : '' }} )</h3>
			</th>
		</tr>
		<tr><th>&nbsp;</th></tr>
		<tr>
			<th>No</th>
			<th>Subject</th>
			<th>Submitter</th>
			<th>Status</th>
            <th>Priority</th>
            <th>Created At</th>
			<th>Comment</th>
            <th>Comment By</th>
            <th>Created At</th>
		</tr>
	</thead>
	<tbody>
		@foreach($report as $key => $value)
            <tr>
				<td>{{ $loop->iteration }}</td>
				<td style="width:150px;">{{ $value->subject }}</td>
				<td style="width:120px;">{!! \globals::get_username($value->submitter) !!}</td>
				<td style="width:120px;">{!! \globals::label_qna_status($value->status) !!}</td>
				<td style="width:120px;">
					{!! \globals::label_qna_priority($value->priority) !!}
				</td>
                <td style="width:140px;">
					{{ $value->created_submitter }}
				</td>
				<td style="width:250px;">
					{{ $value->content }}
				</td>
                <td style="width:140px;">
					{!! \globals::get_username($value->comment_by) !!}
				</td>
				<td style="width:140px;">
					{{ $value->created_comment }}
				</td>
			</tr>
        @endforeach
	</tbody>
</table>