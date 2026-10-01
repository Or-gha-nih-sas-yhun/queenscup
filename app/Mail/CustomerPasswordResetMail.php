<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $customerName,
        public string $resetUrl,
        public int $minutes = 60,
    ) {
    }

    public function build(): self
    {
        return $this->subject("Reset your Queen's Cup password")
            ->text('emails.customer-password-reset');
    }
}
