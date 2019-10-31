<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Airport;


class Flight extends Model
{
    public function arrivalAirport() {
    	return $this->belongsTo('Airport', 'arrivalAirport_id');
    }

    public function departureAirport() {
    	return $this->belongsTo('Airport', 'departureAirport_id');
    }

    public function passengers() {
    	return $this->belongsToMany('App\Customer', 'flight_customer');
    }
}
