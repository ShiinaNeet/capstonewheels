<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PendingEnrollmentsNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $enrollment;
    protected $serviceName;

    public function __construct($enrollment, $serviceName)
    {
        $this->enrollment = $enrollment;
        $this->serviceName = $serviceName;
    }

    public function build()
    {
        return $this->markdown('wheels.email.pendingenrollments', [
            'enrollment' => $this->enrollment,
            'service' => $this->serviceName,
        ])->subject('Pending Enrollment Notification');
    }
}
