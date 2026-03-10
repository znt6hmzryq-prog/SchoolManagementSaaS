<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\School;

class SchoolRegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $school;

    public function __construct(School $school)
    {
        $this->school = $school;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'School Registration Confirmation',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.school_registration_confirmation',
            with: ['school' => $this->school],
        );
    }
}