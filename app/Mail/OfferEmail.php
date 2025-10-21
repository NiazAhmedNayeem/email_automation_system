<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\EmailTemplate;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OfferEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $client;
    public $property;
    public $template;

    public function __construct(Client $client, Property $property, EmailTemplate $template)
    {
        $this->client = $client;
        $this->property = $property;
        $this->template = $template;
    }

    /**
     * Get the message envelope.
     */
    public function build(){

        $subject = str_replace('{{name}}', $this->client->name, $this->template->subject);

        $body = str_replace(
            ['{{name}}', '{{property_title}}'],
            [$this->client->name, $this->property->title],
            $this->template->body
        );

        return $this->subject($subject)
                    ->view('emails.offer')
                    ->with(['body' => $body]);
    }
}
