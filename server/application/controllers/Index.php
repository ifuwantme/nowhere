<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->database();
		$this->load->library('BaiduMapApi_util');
		$this->load->library('nick_util');
		$this->load->model('relation_model');
	}
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('index');
	}

	public function search(){
		$start 		= $this->input->get("start");

		$time 		= $this->input->get("time");

		$loc = $this->baidumapapi_util->getCode($start);
		$loc['address'] = '地铁';
		$loc['distance'] = 2000;
		$stat = $this->baidumapapi_util->getPlace($loc,0);


		$station = $this->db->query("select * from nw_subwaystation where name='".$stat['name']."'")->result();

		$nick = 'L'.$station[0]->line_id.'S'.$station[0]->site;
		$rela_time = $time-$stat['distance'];
		$result = $this->db->query("select * from nw_stationrela where start='".$nick."' and time between ".$rela_time."  and ".$time)->result();

		$end = array();
		foreach($result as $r){
			 $end[$this->nick_util->decode($r->end)] = $r->time;
		}
		$end = array_unique($end);

		foreach($end as $key=>$value){
			echo $key;
		}
//		$data = array();
//		if($result){
//			echo json_encode($result);
//		}


	}

	public function testApi(){
		$address = $this->input->get('address');

		$loc = $this->baidumapapi_util->getCode($address);
		$loc['address'] = '地铁';
		$loc['distance'] = 2000;
		$this->baidumapapi_util->getPlace($loc);
	}

	public function updateInfo(){
//2017-07-30
		$relation = $this->db->query("select * from nw_stationrela where created_at < '2017-07-30 00:00:00'limit 3000")->result();
		foreach($relation as $rela){

			$loc_start = $this->nick_util->decode($rela->start);
			$loc_end = $this->nick_util->decode($rela->end);

			$loc = $this->baidumapapi_util->getDirection($this->baidumapapi_util->getCode($loc_start),$this->baidumapapi_util->getCode($loc_end));
			if($loc !='1') {
				$r = array(
						'id' => $rela->id,
						'time_line' => $loc->result->routes[0]->duration/60

				);

				$this->relation_model->save($r);
			}
		}
	}

	public function updatePlace(){
		$place = $this->db->query("select * from  nw_subwaystation ")->result();

		foreach($place as $p){
			$this->baidumapapi_util->getCode($p->name);
		}
	}

}
