@include('mail.header')
<table role="presentation" class="main">
  <tr>
    <td class="wrapper">
      <table role="presentation" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td>
            <hr class="hr-custom">
            Dear {{ !empty($details['client_name']) ? $details['client_name'] : '' }}, <br><br>
            Welcome to Prolinks! Your account has been successfully created. To ensure the security of your account, please follow the instructions below to set up your password:<br>
            Please click the button below to create your password: <br><br>
            <a href="{{ !empty($details['link']) ? $details['link'] : '' }}" class="btn-custom">Create Password</a><br><br>
            If the above link does not open a new page, you can copy and paste the following URL into your browser:<br>
            <a href="{{ !empty($details['link']) ? $details['link'] : '' }}">{{ !empty($details['link']) ? $details['link'] : '' }}</a> <br><br><br>
            If you have any questions or encounter any issues, feel free to reach out to our support team.<br><br>
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