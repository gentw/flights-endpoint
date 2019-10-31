<?php
namespace App\Services\v1;


use App\Flight;

/**
 * 
 */
class FlightService {
	protected $supportedIncludes = [
		'arrivalAirport' 	=> 'arrival',
		'departureAirport' 	=> 'departure'
 	];

	public function getFlights($params) {
		//return Flight::all();
		if(empty($params)) {
			return $this->filterFlights(Flight::all());
		}

		$withKeys = [];

		if(isset($params['include'])) {
			$includeParams = explode(',', $params['include']);
			$includes = array_intersect($this->supportedIncludes, $includeParams);
			$withKeys = array_keys($includes);
		}

		return $this->filterFlights(Flight::with($withKeys)->get(), $withKeys);
	}

	public function getFlight($flightNumber) {
		return $this->filterFlights(Flight::where('flightNumber', $flightNumber)->get());
	}

	protected function filterFlights($flights, $keys = []) {
		$data = [];

		foreach($flights as $flight) {
			$entry = [
				'flightNumber' => $flight->flightNumber,
				'status'	   => $flight->status,
				//'href' 		   => route('flights.show', ['id' => $flight->flightNumber])
				'href'	       => url('/api/v1/flights/'.$flight->flightNumber)
			];

			if (in_array('arrivalAirport', $keys)) {
                $entry['arrival'] = [
                    'datetime' => $flight->arrivalDateTime,
                    'iataCode' => $flight->arrivalAirport->iataCode,
                    'city' => $flight->arrivalAirport->city,
                    'state' => $flight->arrivalAirport->state,
                ];
            }
            if (in_array('departureAirport', $keys)) {
                $entry['departure'] = [
                    'datetime' => $flight->departureDateTime,
                    'iataCode' => $flight->departureAirport->iataCode,
                    'city' => $flight->departureAirport->city,
                    'state' => $flight->departureAirport->state,
                ];
            }

			$data[] = $entry;
		}

		return $data;		
	}

}

