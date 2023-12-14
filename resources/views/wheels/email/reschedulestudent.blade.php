@component('mail::message')
#
<p style="font-size:20px;font-weight:600;color:#154EC1;">Reschedule Enrollment</p>
<br/>
<p>Dear Student,</p>
<p>Your enrollment "{{ $service }}" is now rescheduled to batch no. {{ $batch }} ({{ $date }}).</p>
<br/>
<p>Best regards,<br/>{{ env('APP_NAME') }}</p>
@endcomponent
