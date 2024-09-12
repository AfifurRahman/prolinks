@include('mail.header')
<table role="presentation" class="main">
  <tr>
    <td class="wrapper">
      <table role="presentation" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td>
            <hr class="hr-custom">
            Dear <b>{{ $details['receiver'] }}</b>, <br>
            We are pleased to inform you that the <b>Project {{ $details['project_name'] }}</b> has been successfully terminated. You can download all project files using the link below:<br><br>
            <a href="{{ $details['url']}}" style="word-wrap: break-word;">{{$details['url'] }}</a><br><br>
            Please note that this link will expire in 7 days, after which it will no longer be accessible.<br><br>
            If you have any questions or require further assistance, please feel free to reach out to us at <a href="mailto:cs@prolinks.id">cs@prolinks.id</a>.<br><br>
            Regards, <br>
            Admin Prolinks
            <hr class="hr-custom">
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
@include('mail.footer')