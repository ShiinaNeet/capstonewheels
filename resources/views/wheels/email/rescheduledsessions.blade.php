@component('mail::message')
# Session Rescheduled Notification

Your session has been rescheduled. Please check the new details below:

- **Service:** {{ $service->name }}

@foreach($service_schedule as $schedule)
    - **Date:** {{ $schedule['day_of_week'] }} at {{ $schedule['time_start'] }} to {{ $schedule['time_end'] }}
@endforeach

Thank you for using our service!

@endcomponent
