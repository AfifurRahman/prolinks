@include('mail.header')
<table role="presentation" class="main">
  <tr>
    <td class="wrapper">
      <table role="presentation" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td>
            <hr class="hr-custom">
            Dear, {{ !empty($details['client_name']) ? $details['client_name'] : '' }} <br><br>
            Your account has been created, <br>
            please create your password click on the button below : <br><br>
            <a href="{{ !empty($details['link']) ? $details['link'] : '' }}" class="btn-custom">Create Password</a><br><br>
            Or copy and paste the URL into your browser<br>
            <a href="{{ !empty($details['link']) ? $details['link'] : '' }}">{{ !empty($details['link']) ? $details['link'] : '' }}</a> <br><br><br>
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