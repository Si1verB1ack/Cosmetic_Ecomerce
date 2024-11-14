<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class NewProductEmail extends Mailable
{
    use Queueable, SerializesModels;


    public $mailData;
    public $largeImagePath;

    /**
     * Create a new message instance.
     */
    public function __construct($mailData, $largeImagePath)
    {
        $this->mailData = $mailData;
        $this->largeImagePath = $largeImagePath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailData['mail_subject'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.new-product',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->largeImagePath)
                ->as('large-product-image.jpg')
                ->withMime('image/jpeg')
        ];
    }
}
