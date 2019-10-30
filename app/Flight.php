<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Airport;
use App\Customer;

class Flight extends Model
{
    public function arrivalAirport() {
    	return $this->belongsTo('Airport', 'arrivalAirport_id');
    }

    public function departureAirport() {
    	return $this->belongsTo('Airport', 'departureAirport_id');
    }

    public function passengers() {
    	return $this->belongsToMany('Customer', 'flight_customer');
    }
}
