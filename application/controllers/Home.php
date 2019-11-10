<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model("home_m");
	}
	private function is_connected()
	{
	    $connected = @fsockopen("www.google.com", 80); 
	    if ($connected){
	        $is_conn = true;
	        fclose($connected);
	    }else{
	        $is_conn = false;
	    }
	    return $is_conn;
	}
	public function index()
	{
		$data['subview'] = 'home/index';
		$this->load->view('_layout_main',$data);
	}

	public function get_postal_code_details()
	{
		if (!$this->is_connected()) die(http_response_code(428)); // checking internet conn

		$pin = $this->input->post('pin');
		$ret = [];
		$send = [];
		// postal code should be number and of 6 digit
		if (is_numeric($pin) && strlen($pin) == 6) {
			$data1 = $this->home_m->get_pin($pin); //getting from database
			if ($data1) {
				unset($data1->id);
				unset($data1->created_at);
				$ret = $data1;
			} else {
				$data2 = json_decode($this->home_m->call_api($pin)); // getting from api if not present in db
				if ($data2->Status == 'Success') {
					$save['postal_code'] = $pin;
					$save['district'] = $data2->PostOffice[0]->District;
					$save['state'] = $data2->PostOffice[0]->State;
					$this->home_m->save_pin($save); // saving new pin code to database
					$ret = $save;
				} else {
					$send['message'] = 'Postal Code not found';
				}
			}
		} else {
			$send['message'] = 'Please Enter 6 Digit Postal Code';
		}
		if (empty($ret)) {
			$send['status'] = 'error';
			http_response_code(422);
		} else {
			$send['status'] = 'success';
		}
		$send['data'] = $ret;
		
		echo json_encode($send);
	}

}
