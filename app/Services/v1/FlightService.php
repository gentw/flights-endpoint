<?php
namespace App\Services\v1;


use App\Flight;
use App\Airport;

/**
 * 
 */
class FlightService {
	protected $supportedIncludes = [
		'arrivalAirport' 	=> 'arrival',
		'departureAirport' 	=> 'departure'
 	];

 	protected $clauseProperties = [
        'status',
        'flightNumber'
    ];

	public function getFlights($params) {
		//return Flight::all();
		if(empty($params)) {
			return $this->filterFlights(Flight::all());
		}

		$withKeys = $this->getWithKeys($params); 
		$whereClauses = $this->getWhereClause($params);

		$flights = Flight::with($withKeys)->where($whereClauses)->get();

		return $this->filterFlights($flights, $withKeys);
		//return $this->filterFlights(Flight::with($withKeys)->get(), $withKeys);
	}

	public function createFlight($req) {
		$arrivalAirport = $req->input('arrival.iataCode');
        $departureAirport = $req->input('departure.iataCode');
        $airports = Airport::whereIn('iataCode', [$arrivalAirport, $departureAirport])->get();
        $codes = [];

        foreach($airports as $port) {
        	$codes[$port->iataCode] = $port->id;
        }

        $flight = new Flight();
        $flight->flightNumber = $req->input('flightNumber');
        $flight->status = $req->input('status');
        $flight->arrivalAirport_id = $codes[$arrivalAirport];
        $flight->arrivalDateTime = $req->input('arrival.datetime');
        $flight->departureAirport_id = $codes[$departureAirport];
        $flight->departureDateTime = $req->input('departure.datetime');
        $flight->save();
        return $this->filterFlights([$flight]);
	}


	// public function getFlight($flightNumber) {
	// 	return $this->filterFlights(Flight::where('flightNumber', $flightNumber)->get());
	// }

	protected function filterFlights($flights, $keys = [], $atts = "default") {
		$data = [];

		if($atts != "all") {
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
		} else {
			$data[] = $flights;
		}

		return $data;		
	}

	protected function getWithKeys($params) {
		$withKeys = [];

		if(isset($params['include'])) {
			$includeParams = explode(',', $params['include']);
			$includes = array_intersect($this->supportedIncludes, $includeParams);
			$withKeys = array_keys($includes);
		}

		return $withKeys;
	}

	protected function getWhereClause($params) {
		$clause = [];

		foreach($this->clauseProperties as $prop) {
			if(in_array($prop, array_keys($params))) {
				$clause[$prop] = $params[$prop];
			}
		}

		return $clause;
	}


}

