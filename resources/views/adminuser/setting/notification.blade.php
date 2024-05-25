@extends('layouts.app_client')

@section('content')
    <div class="card-box" style="width: 50%; margin: 0 auto; margin-top: 10px;">
        <h4>Notifications</h4> <hr>
        <div class="user-list notify-list">
            @if (count($notification))
                @foreach ($notification as $notify)
                    <li style="list-style:none; border-bottom: 1px solid #EEEEEE !important; padding:10px 12px !important; background-color: {{ $notify->is_read == 1 ? '':'#F5F5F5' }};">
                        <a href="#" data-url="{{ $notify->link }}" data-id="{{ $notify->id }}" onclick="readNotification(this)" class="user-list-item">
                            @if ($notify->type == 0)
                                <div class="icon bg-warning">
                                    <i class="mdi mdi-comment"></i>
                                </div>
                            @elseif($notify->type == 1)
                                    <div class="icon bg-info">
                                    <i class="mdi mdi-file"></i>
                                </div>
                            @endif
                            <div class="user-desc" style="margin-top:-20px; text-overflow:ellipsis;">
                                <span class="name"><b>{{ $notify->sender_name }}</b> - {{ $notify->text }}</span>
                                <span class="time">{{ $notify->created_at }}</span>
                            </div>
                        </a>
                    </li>
                @endforeach
            @endif
        </div>
    </div>
@stop

@push('scripts')
    <script type="text/javascript">
		function readNotification(element) {
            var id = $(element).data('id');
            var url = $(element).data('url');
            $.ajax({
                type: "POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    "id": id
                },
                url: "{{ route('notification.read') }}",
                success:function(output) {
                    window.location.href = url;
                }
            });
        }
	</script>
@endpush