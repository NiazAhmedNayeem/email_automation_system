<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = ['client_id', 'property_id', 'status', 'scheduled_at'];

    public function client(){
        return $this->belongsTo(Client::class, 'client_id');
    }
    public function property(){
        return $this->belongsTo(Property::class, 'property_id');
    }
}
