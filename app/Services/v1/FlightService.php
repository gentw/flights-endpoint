<?php
namespace App\Services\v1;


use App\Flight;

/**
 * 
 */
class FlightService {
	
	public function getFlights() {
		//return Flight::all();
		return $this->filterFlights(Flight::all());
	}

	public function getFlight($flightNumber) {
		return $this->filterFlights(Flight::where('flightNumber', $flightNumber)->get());
	}

	protected function filterFlights($flights) {
		$data = [];

		foreach($flights as $flight) {
			$entry = [
				'flightNumber' => $flight->flightNumber,
				'status'	   => $flight->status,
				//'href' 		   => route('flights.show', ['id' => $flight->flightNumber])
				'href'	       => url('/api/v1/flights/'.$flight->flightNumber)
			];

			$data[] = $entry;
		}

		return $data;		
	}

}

