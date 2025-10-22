<?php

use App\Console\Commands\SendPendingOffers;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('offers:send', function () {
    $command = new \App\Console\Commands\SendPendingOffers();
    $command->handle();
})->describe('Send scheduled emails');

