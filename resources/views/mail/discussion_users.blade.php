@include('mail.header')
<table role="presentation" class="main">
  <tr>
    <td class="wrapper">
      <table role="presentation" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td>
            <hr class="hr-custom">
            Dear {{ !empty($details['receiver_name']) ? $details['receiver_name'] : '' }}, <br>
            New project discussion created from <b>{{ !empty($details['discussion_creator']) ? $details['discussion_creator'] : '' }}</b><br><br>
            <b>Project :</b> {{ !empty($details['project_name']) ? $details['project_name'] : '' }}<br>
            <b>Subject :</b> {{ !empty($details['subject']) ? $details['subject'] : '' }}<br>
            <b>Comment :</b> {{ !empty($details['comment']) ? $details['comment'] : '' }}<br><br>
            You can view the discussion on the following link:<br>
            <a href="{{ !empty($details['link']) ? $details['link'] : '' }}">{{ !empty($details['link']) ? $details['link'] : '' }}</a>
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