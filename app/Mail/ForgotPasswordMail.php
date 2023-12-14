<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $password;
    protected $user_type;

    public function __construct(String $password, String $user_type)
    {
        $this->password = $password;
        $this->user_type = $user_type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('wheels.email.accounts.resetpw', [
            'password' => $this->password,
            'user_type' => $this->user_type
        ])->subject('Your new account credential!');
    }
}
