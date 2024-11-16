<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;

class NewProductEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;
    public $largeImagePath;
    private $imageCid;

    /**
     * Create a new message instance.
     */
    public function __construct($mailData, $largeImagePath)
    {
        $this->mailData = $mailData;
        $this->largeImagePath = $largeImagePath;
    }

    /**
     * Build the message content.
     */
    public function build()
    {
        $this->view('email.new-product')
            ->subject($this->mailData['mail_subject']);

        // Embed the image using Symfony Mailer
        $this->withSymfonyMessage(function (Email $message) {
            $this->imageCid = $message->embedFromPath($this->largeImagePath, 'large-product-image', 'image/jpeg');
        });

        return $this;
    }

    /**
     * Pass the CID to the view for inline display.
     */
    public function getImageCid()
    {
        return $this->imageCid;
    }
}
