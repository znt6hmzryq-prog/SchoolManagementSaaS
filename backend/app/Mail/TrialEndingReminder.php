<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\School;

class TrialEndingReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $school;
    public $daysLeft;

    public function __construct(School $school, $daysLeft)
    {
        $this->school = $school;
        $this->daysLeft = $daysLeft;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Trial Ending Reminder',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.trial_ending_reminder',
            with: ['school' => $this->school, 'daysLeft' => $this->daysLeft],
        );
    }
}