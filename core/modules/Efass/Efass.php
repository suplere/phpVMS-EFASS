<?php

 
class Efass extends CodonModule
{
	
	public function index()
	{
		// Get all of our hubs, and list pilots by hub
		$allhubs = OperationsData::GetAllHubs();
		
		if(!$allhubs) $allhubs = array();
		
		foreach($allhubs as $hub)
		{
			$this->set('title', $hub->name);
			$this->set('icao', $hub->icao);
			
			$this->set('allpilots', PilotData::findPilots(array('p.hub'=>$hub->icao)));
								
			$this->render('pilots_list.tpl');
		}
		
		$nohub = PilotData::findPilots(array('p.hub'=>''));
		if(!$nohub)
		{
			return;
		}
		
		$this->set('title', 'No Hub');
		$this->set('icao', '');
		$this->set('allpilots', $nohub);
		$this->render('pilots_list.tpl');
	}

	public function altgraph($pirepid)
	{
		$data = EFASSData::getAltitudeprofile($pirepid);
		$this->create_line_graph('Ground altitude profile', $data);
	}

	public function speedgraph($pirepid)
	{
		$data = EFASSData::getSpeedprofile($pirepid);
		$this->create_line_graph('Ground speed profile', $data);
	}

	protected function create_line_graph($title, $data)
	{	
		if(!$data)
		{
			$data = array();
		}
				
		$bar_values = array();
		$bar_titles = array();
		foreach($data as $val)
		{
			
			$bar_titles[] = $val->ym;
			$bar_values[] = round(floatval($val->total));
		}
	
		OFCharts::add_data_set($bar_titles, $bar_values);
		echo OFCharts::create_area_graph($title);
	}

}
