@component('mail::message')
#
<p style="font-size:20px;font-weight:600;color:#154EC1;">Reset Password</p>
<br/>
<p>Dear {{ ucfirst($user_type) }},</p>

<p>Your account password reset was successful. Please find your new password below. To ensure the safety of your account, we strongly encourage you to update your password.</p>
<p>Password: &nbsp;<span style="font-size:18px;font-weight:600;">{{ $password }}</span></p>
<br/>
<p>Best regards,<br/>{{ env('APP_NAME') }}</p>
@endcomponent
