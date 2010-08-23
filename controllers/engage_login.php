<?php
class Engage_login extends Controller{
	function __construct(){
		parent::__construct();
		$this->load->library('engage');
	}
	
	function index(){
		// Check for token
		if(!$this->input->get('token')){
			//NO TOKEN GIVEN!
		}
		
		// Send token to RPX Library
		$this->engage->token($this->input->get('token'));
		// auth_info API Call
		$response = $this->engage->authinfo();
		
		// Print out user info (just as an example)
		print_r($response);
	}
}
?>
