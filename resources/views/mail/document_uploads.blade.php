@include('mail.header')
<table role="presentation" class="main">
  <tr>
    <td class="wrapper">
      <table role="presentation" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td>
            <hr class="hr-custom">
            Hello <b>{{ $details['receiver'] }}</b>, <br>
            New file has been uploaded on <b>{{$details['project_name']}}</b> by <b>{{$details['uploader']}}</b><br><br>
            <b>Filename :</b> {{ $details['file_name'] }}<br>
            <b>Size :</b> {{ $details['file_size'] }}<br><br>
            You can view the file on this following link below :<br>
            <div style="width:480px;">
              <a href="{{ !empty($details['url']) ? $details['url'] : '' }}" style="word-wrap: break-word;">{{$details['url'] }}</a>
            </div>
            <br><br>
            Regards, <br>
            Admin
            <hr class="hr-custom">
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
@include('mail.footer')