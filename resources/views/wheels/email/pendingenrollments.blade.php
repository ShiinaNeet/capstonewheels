@component('mail::message')
# 

<p>Dear student,</p>

<p>We hope this message finds you well. It appears that your enrollment has been pending for 24 hours without completion.</p>

<p>Details:</p>
<ul>
    <li><strong>Service name:</strong> {{ $service }} </strong></li>
    <li><strong>Batch:</strong> {{ $enrollment->batch }} </li>
    <!-- Add other fields you want to display -->
</ul>

<p style="margin-top: 20px;">
    Unfortunately, your enrollment has been canceled due to inactivity to continue the enrollment. If you still wish to enroll, please click the link below:
</p>

<p style="margin-top: 10px;">
    <a href="https://wheelscapstone.site">Enroll Now</a>
</p>

<p style="margin-top: 20px;">
    If you have any questions or need further assistance, feel free to contact us.
</p>

<p style="margin-top: 20px;">Best regards,<br>{{ env('APP_NAME') }}</p>
@endcomponent
