<?php

class Home_m extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}

	public function get_pin($pin)
	{
		$data = $this->db->where('postal_code',$pin)->get('pincode')->row();
		return $data;
	}		
	public function call_api($pin)
	{
		$ch = curl_init();
		$curlConfig = array(
		    CURLOPT_URL            => "http://www.postalpincode.in/api/pincode/$pin",
		    CURLOPT_RETURNTRANSFER => true,
		);
		curl_setopt_array($ch, $curlConfig);
		$data = curl_exec($ch);
		curl_close($ch);
		// $data = file_get_contents("http://www.postalpincode.in/api/pincode/$pin");
		return $data;
	}
	public function save_pin($save)
	{
		$this->db->insert('pincode',$save);
		return true;
		// return $this->get_pin($save['postal_code']);
	}
}