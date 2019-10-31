<?php
namespace App\Services\v1;


use App\Flight;

/**
 * 
 */
class FlightService
{
	
	function getFlights() {
		return Flight:all();
	}
}