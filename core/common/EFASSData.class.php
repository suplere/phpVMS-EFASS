<?php
/**
 * phpVMS - Virtual Airline Administration Software
 * Copyright (c) 2008 Nabeel Shahzad
 * For more information, visit www.phpvms.net
 *	Forums: http://www.phpvms.net/forum
 *	Documentation: http://www.phpvms.net/docs
 *
 * @author Evzen Supler
 * @copyright Copyright (c) 2014, Evzen Supler
 * @
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/
 */

class EFASSData extends CodonData
{	
	public static $lasterror;
	public static $pirepid;
	
	/*Save EFASS flight data to the table TABLE_PREFIX.'efassflightdata
	structure:
	1	id_rec	int(11)			Ne	Žádná	AUTO_INCREMENT
	2	idefassflight	varchar(50)	
	3	time	time			
	 4	lat	varchar(15)	
	 5	lon	varchar(15)	
	 6	alt	varchar(6)	
	 7	ias	int(11)	
	 8	gs	int(11)	
	 9	vs	varchar(8)	
	 10	galt	varchar(6)
	 11	hdg	smallint(6)	
	 12	qnh	smallint(4)	
	 13	sq	smallint(4)	
	 14	stage	varchar(255)
	 15	l_ldg	tinyint(1)
	 16	l_str	tinyint(1)
	 17	l_bea	tinyint(1)
	 18	l_nav	tinyint(1)
	 19	l_gnd	tinyint(1)
	 20	l_log	tinyint(1)
	 
	 
	 */
	 
	 public static function SaveEfassFD($edata)
	 {
	 Debug::log(print_r($edata, true), 'efass');
	 $temp = array();
	 $temp['alt'] = $edata['efass_calt'];
	 $temp['galt'] = $edata['efass_groundaltitude'];
	 $temp['ias'] = $edata['efass_ias'];
	 $temp['gspeed'] = $edata['efass_groundspeed'];
	 #EFASS serve altitude in meters by default
	 if(Config::Get('AltUnit') == '1')
	 {
		$temp['alt'] = $temp['alt'] * 3.281;
		$temp['galt'] = $temp['galt'] * 3.281;
	 }
	 #EFASS serve speed in knots by default
	 if(Config::Get('SpeedUnit') == '0')
	 {
		$temp['ias'] = $temp['ias'] * 1.852;
		$temp['gspeed'] = $temp['gspeed'] * 1.852;
		
	 }
	 
	 $sql = "INSERT INTO ".TABLE_PREFIX."efassflightdata(	
							`idefassflight`, 
							`time`, 
							`lat`, 
							`lon`, 
							`alt`, 
							`ias`,
							`gs`,
							`vs`,
							`galt`,
							`hdg`, 
							`qnh`,
							`sq`,
							`stage`, 
							`l_ldg`, 
							`l_str`,
							`l_bea`,
							`l_nav`,
							`l_gnd`,
							`l_log`)
					VALUES ( '{$edata['efass_uniqueflightid']}', 
							NOW(), 
							'{$edata['efass_lat']}', 
							'{$edata['efass_lon']}', 
							'{$temp['alt']}', 
							{$temp['ias']},
							{$temp['gspeed']},
							'{$edata['efass_vs']}',
							'{$temp['galt']}', 
							{$edata['efass_hdg']}, 
							{$edata['efass_pressuresetting']},
							{$edata['efass_transponder']},
							'{$edata['efass_flightstage']}', 
							{$edata['efass_landinglights']},
							{$edata['efass_strobelights']},
							{$edata['efass_beaconlights']},
							{$edata['efass_navlights']},
							{$edata['efass_groundlights']},
							{$edata['efass_logolights']})";

		$ret = DB::query($sql);
		Debug::log($sql, 'efass');
		Debug::log($ret, 'efass');
		return $ret;
	}
	
	public static function PreparePIREPData($pilotid,$edata)	
	{
		Debug::log(print_r($edata, true), 'efass');		
		
		$flightinfo = SchedulesData::getProperFlightNum($edata['efass_flightnumber']);
		$code = $flightinfo['code'];
		$flightnum = $flightinfo['flightnum'];
		
		#  If not, add them.
		$depicao = $edata['efass_origin'];
		$arricao = $edata['efass_destination'];
		
		if(!OperationsData::GetAirportInfo($depicao))
		{
			OperationsData::RetrieveAirportInfo($depicao);
		}
		
		if(!OperationsData::GetAirportInfo($arricao))
		{
			OperationsData::RetrieveAirportInfo($arricao);
		}
		
		# Get aircraft information
		$reg = trim($edata['efass_registration']);
		$ac = OperationsData::GetAircraftByReg($reg);
		
		# Load info
		/* If no passengers set, then set it to the cargo */
		$load = $edata['efass_pob'];
		if($load == 0)
			$load = $edata['efass_cargo'];
		
		# Convert the time to xx.xx 
		$flighttime = gmdate("H:i",$edata['efass_t_onblock']-$edata['efass_t_offblock']);
		
		/* Fuel conversion - EFASS reports in kg */
		$fuelused = $edata['efass_plannedfuel'];
		#Convert to liters
		if(Config::Get('LiquidUnit') == '0')
		{
			# Convert to KGs, divide by density since d = mass * volume
			$fuelused = $fuelused / .8075;
		}
		# Convert kg to gallons
		elseif(Config::Get('LiquidUnit') == '1')
		{
			$fuelused = $fuelused * 6.84 / .45359237;
		}
		# Convert kg to lbs
		elseif(Config::Get('LiquidUnit') == '3')
		{
			$fuelused = $fuelused / .45359237;
		}
		
		$acars_data = ACARSData::get_flight_by_pilot($pilotid);
		
		$data = array(
			'pilotid'=>$pilotid,
			'code'=>$code,
			'flightnum'=>$flightnum,
			'depicao'=>$depicao,
			'arricao'=>$arricao,
			'aircraft'=>$ac->id,
			'flighttime'=>$flighttime,
			'submitdate'=>'NOW()',
			'route' => $acars_data->route,
			'route_details' => $acars_data->route_details,
			'distance'=>$edata['efass_dcomplete'],
			'comment'=>$edata['efass_pireptext'],
			'fuelused'=>$fuelused,
			'source'=>'efass',
			'load'=>$load,
			'efass_uniqueflightid'=>$edata['efass_uniqueflightid'],
			'log'=> 'Manual pirep from EFASS'
		);
				
		Debug::log(print_r($data, true), 'efass');
		
		$ret = ACARSData::FilePIREP($pilotid, $data);
				
		return $ret;
	}
		
		
}
