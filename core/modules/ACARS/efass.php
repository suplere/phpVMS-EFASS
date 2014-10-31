<?php

/**
 * phpVMS ACARS integration
 *
 * Interface for use with XACARS
 * http://www.xacars.net/
 *
 *
 * This file goes as this:
 *	The URL given is:
 *		<site>/index.php/acars/xacars/<action>
 *
 * SDK Docs: http://www.xacars.net/index.php?Client-Server-Protocol
 */

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 'on');

//Debug::log($_SERVER['QUERY_STRING'], 'efass');
//Debug::log($_SERVER['REQUEST_URI'], 'efass');
//Debug::log(print_r($_REQUEST), 'efass');

class Coords {
	public $lat;
	public $lng;
}

switch($acars_action)
{
	case 'efass':
		# Pass success by default DISPATCHING, BOARDING, TAXI-OUT, TAKEOFF, DEPARTURE, FLIGHT, ARRIVAL, ROLLOUT, TAXI-IN,DEBOARDING
		$outstring = 'Success';
		$sendPIREP = False;
		$fields = array();
		#Write EFASS Flight Stage
		$res = EFASSData::SaveEfassFD($_REQUEST);
		$pilotinfo = PilotData::getPilotByEmail($_REQUEST['efass_email']);
		Debug::log(print_r($pilotinfo, true), 'efass');

		$pilotid = $pilotinfo->pilotid;

		#EFASS serve altitude in meters by default
		if(Config::Get('AltUnit') == '1')
		{
			$_REQUEST['efass_calt'] = $_REQUEST['efass_calt'] * 3.281;

		}
		#EFASS serve speed in knots by default
		if(Config::Get('SpeedUnit') == '0')
		{

			$_REQUEST['efass_groundspeed'] = $_REQUEST['efass_groundspeed'] * 1.852;
		}
		$fields = array(
			'flightnum'=>$_REQUEST['efass_flightnumber'],
			'aircraft'=>$_REQUEST['efass_aircraft'],
			'lat'=>$_REQUEST['efass_lat'],
			'lng'=>$_REQUEST['efass_lon'],
			'heading'=>$_REQUEST['efass_hdg'],
			'route'=>$_REQUEST['efass_route'],
			'alt'=>$_REQUEST['efass_calt'],
			'gs'=>$_REQUEST['efass_groundspeed'],
			'depicao'=>$_REQUEST['efass_origin'],
			'arricao'=>$_REQUEST['efass_destination'],
			'deptime'=>$_REQUEST['efass_std'],
			'online'=>$_REQUEST['efass_network'],
			'client'=>'efass',
			);

		switch(strtoupper($_REQUEST['efass_flightstage']))
		{
			case 'DISPATCHING':
				$fields['phasedetail'] = 'Dispatching';
				break;

			case 'BOARDING':
				$fields['phasedetail'] = 'Boarding';
				break;

			case 'TAXI OUT':
				$fields['phasedetail'] = 'Taxiing out';
				break;

			case 'TAKEOFF':
				$fields['phasedetail'] = 'Take off';
				break;

			case 'DEPARTURE':
				$fields['phasedetail'] = 'Departure';
				break;

			case 'FLIGHT':
				$fields['phasedetail'] = 'Enroute';
				break;

			case 'ARRIVAL':
				$fields['phasedetail'] = 'Arriving';
				break;

			case 'ROLLOUT':
				$fields['phasedetail'] = 'Rollout';
				break;

			case 'TAXI IN':
				$fields['phasedetail'] = 'Taxiing in';
				#PIREP MAY BE SEND
				if ($_REQUEST['efass_ispirep'] == 1)
				{
					$sendPIREP = True;
				}
				break;

			case 'DEBOARDING':
				$fields['phasedetail'] = 'Deboarding';
				#PIREP MAY BE SEND
				if ($_REQUEST['efass_ispirep'] == 1)
				{
					$sendPIREP = True;
				}
				break;
		}

		# Get the distance remaining

		$dist_remain = $_REQUEST['efass_ddest'];

		# Estimate the time remaining
		if($_REQUEST['efass_groundspeed'] > 0)
		{
			$time_remain = $dist_remain / $_REQUEST['efass_groundspeed'] * 60 * 60;
			$time_remain = gmdate("H:i", $time_remain);
		}
		else
		{
			$time_remain = '00:00';
		}

		$fields['distremain'] = $dist_remain;
		$fields['timeremaining'] = $time_remain;

		Debug::log(print_r($fields, true), 'efass');
		ACARSData::UpdateFlightData($pilotid, $fields);
		if ($sendPIREP)
		{
			$res = EFASSData::PreparePIREPData($pilotid,$_REQUEST);
		}
		echo '1|'.$outstring;
		break;

}
