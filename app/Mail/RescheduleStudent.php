<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RescheduleStudent extends Mailable
{
    use Queueable, SerializesModels;

    protected $service;
    protected $date;
    protected $batch;
    protected $student;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(String $service, $date, $batch)
    {
        $this->service = $service;
        $this->date = $date;
        $this->batch = $batch;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('wheels.email.reschedulestudent', [
            'service' => $this->service,
            'date' => $this->date,
            'batch' => $this->batch,
        ])->subject('Enrollment Rescheduled');
    }
}
