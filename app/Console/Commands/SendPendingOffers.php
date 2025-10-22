<?php

namespace App\Console\Commands;

use App\Mail\OfferEmail;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\Offer;
use Illuminate\Console\Command;

class SendPendingOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offers:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send pending offers emails automatically';

    /**
     * Execute the console command.
     */


    public function handle()
    {
        $offers = Offer::where('status', 'pending')->get();
        $template = EmailTemplate::first();

        if (!$template) {
            $this->error('No email template found!');
            return 0;
        }

        foreach ($offers as $offer) {
            try {
                \Mail::to($offer->client->email)->send(
                    new OfferEmail(
                        $offer->client,
                        $offer->property,
                        $template,
                    )
                );

                // Email Log
                EmailLog::create([
                    'client_id' => $offer->client_id,
                    'offer_id' => $offer->id,
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                // Update Offer status
                $offer->update(['status' => 'sent']);

                $this->info("Email sent to {$offer->client->email}");
            } catch (\Exception $e) {
                $this->error("Failed to send email to {$offer->client->email}: " . $e->getMessage());
            }
        }

        return 0;
    }
}
