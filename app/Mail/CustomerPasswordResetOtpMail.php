<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerPasswordResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $customerName,
        public string $otp,
        public int $minutes = 60,
    ) {
    }

    public function build(): self
    {
        return $this->subject("Your Queen's Cup password reset OTP")
            ->text('emails.customer-password-reset-otp');
    }
}
