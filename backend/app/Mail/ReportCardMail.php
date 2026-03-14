<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportCardMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $filePath;

    /**
     * Create a new message instance.
     */
    public function __construct($student, $filePath)
    {
        $this->student = $student;
        $this->filePath = $filePath;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $mail = $this->subject('Student Report Card')
            ->view('emails.report-card')
            ->with(['student' => $this->student]);

        if ($this->filePath && file_exists($this->filePath)) {
            $mail->attach($this->filePath, [
                'as' => 'report_card_' . $this->student->id . '.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}
