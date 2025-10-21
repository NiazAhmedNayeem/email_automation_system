<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
        protected $fillable = [
        'client_id',
        'offer_id',
        'status',
        'sent_at',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
