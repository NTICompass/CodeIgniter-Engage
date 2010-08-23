<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * NTICompass' CodeIgniter
 * Janrain Engage (RPX) Library v2.0
 * http://codeigniter.com/wiki/Janrain_Engage
 *
 * Requires PHP 5 with JSON and cURL
 * Also requires CodeIgniter to have Query Strings enabled
 *
 * Written by Eric Siegel
 * NTICompass@gmail.com
 * http://NTICompassInc.com
 *
 * Based off code from:
 * https://rpxnow.com/examples/RPX.php
 * and
 * http://codeigniter.com/wiki/OpenIDRpx
 * 
 * Please read the API docs located at:
 * https://rpxnow.com/docs
 * 
 * Product Website:
 * http://www.janrain.com/products/engage
 * 
 * NOTE: Some API calls require a Plus/Pro account
 */
class Engage{
	var $token;
    var $api_key;
	var $token_url;
	var $post_url;
	var $response_data;
	var $CodeIgniter;
	
	/**************************************
	 * Janrain Engage Library Constructor *
	 **************************************/
	function __construct(){		
		// Get CodeIgniter instance
		$this->CodeIgniter =& get_instance();
		
		// Get info from Config File
		$this->CodeIgniter->config->load('engage_conf');
		$this->api_key = $this->CodeIgniter->config->item('api_key', 'RPX');
		$this->token_url = $this->CodeIgniter->config->item('token_url', 'RPX');
		$this->post_url = $this->CodeIgniter->config->item('post_url', 'RPX');
	}
	
	/**
	 * Get token from controller
	 */
	function token($token){
		$this->token = $token;
	}
	
	/*************
	 * API Calls *
	 *************/
	
	/**
	 * auth_info API Call
	 */
	 function authinfo($extended=TRUE){
		$response = $this->cPost("auth_info",
			array(
				'apiKey' => $this->api_key,
				'token' => $this->token,
				'extended' => $extended,
			)
		);

		if($response !== FALSE){
			return json_decode($response, TRUE);
		}
		return FALSE;
	 }
	 
	 /**
	  * get_user_data API Call
	  */
	 function userData($identifier, $extended=TRUE){
		 $response = $this->cPost("get_user_data",
			array(
				'apiKey' => $this->api_key,
				'identifier' => $identifier,
				'extended' => $extended,
			)
		);

		if($response !== FALSE){
			return json_decode($response, TRUE);
		}
		return FALSE;
	 }
	
	/**
	 * (un)map API Call
	 */
	 function map($userID, $identifier='', $overwrite=TRUE, $unmap=FALSE, $all=FALSE, $unlink=FALSE){		
		$map = ($unmap) ? "unmap" : "map";
		
		$data = array(
			'apiKey' => $this->api_key,
			'primaryKey' => $userID,
		);
		
		if($unmap){
			if($identifier != ''){
				$data['identifier'] = $identifier;
			}
			if($all){
				$data['all_identifiers'] = $all;
			}
			if($unlink){
				$data['unlink'] = $unlink;
			}
		}
		else{
			$data['identifier'] = $identifier;
			$data['overwrite'] = $overwrite;
		}
		
		$response = $this->cPost($map, $data);
		
		if($response !== FALSE){
			$json = json_decode($response, TRUE);
			return $json['stat'] === "ok";
		}
		return FALSE;
	 }
	 
	/**
	* (all_)mappings API Call
	*/
	function mappings($userID, $all=FALSE){
		$mappings = $all ? "all_mappings" : "mappings";

		$data['apiKey'] = $this->api_key;

		if(!$all){
			$data['primaryKey'] = $userID;
		}

		$response = $this->cPost($mappings, $data);

		if($response !== FALSE){
			return json_decode($response, TRUE);
		}
		return FALSE;
	}
	
	/**
	 * get_contacts API Call
	 */
	function contacts($identifier){
		$response = $this->cPost("get_contacts",
			array(
				'apiKey' => $this->api_key,
				'identifier' => $identifier,
			));

		if($response !== FALSE){
			$contacts = json_decode($response, TRUE);
			return $contacts['response']['entry'];
		}
		return FALSE;
	}
	
	/**
	 * set_status/activity API Calls
	 */
	function status($identifier, $message, $activity=FALSE){
		$status = (!$activity) ? "set_status" : "activity";
		
		$data = array(
			'apiKey' => $this->api_key,
			'identifier' => $identifier,
		);
		
		if($activity && is_array($message)){
			$data['activity'] = urlencode(json_encode($message));
		}
		else{
			$data['status'] = $message;
		}
		$response = $this->cPost($status, $data);

		if($response !== FALSE){
			$json = json_decode($response, TRUE);
			return $json['stat'] === "ok";
		}
		return FALSE;
	}
	 
	/**************************
	 * cURL HTTP POST Request *
	 **************************/
	 function cPost($api_call, $data){
		// Reset post data
		$response_data = '';
		
		// Initialize cURL session
		$curl = curl_init();
		
		// Setup cURL options
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_URL, "{$this->post_url}$api_call");
		
		// Here we go, send that data...
		$this->response_data = curl_exec($curl);
		
		if(!curl_errno($curl)){
			// Get reponse data
			$response_body = $this->response_data;
			
			// Close cURL connection and flush data
			$response_data = '';
			curl_close($curl);
			
			return $response_body;
		}
		return FALSE;
	 }
	
	/**************************************
	 * Functions to add RPX login to page *
	 **************************************/
	 
	/**
	 * JavaScript to enable RPX Popup
	 */
	function script(){
		$scriptURL = $this->CodeIgniter->config->item('script', 'RPX');
		$scriptTag = "<script type=\"text/javascript\" src=\"$scriptURL\"></script>\n";
		$scriptTag .= "<script type=\"text/javascript\">\n";
		$scriptTag .= "RPXNOW.overlay = true;\n"."RPXNOW.language_preference = 'en';\n";
		$scriptTag .= "</script>\n";
		return $scriptTag;
	 }
	 
	/**
	 * Popup Link
	 */
	function popup($message){
		$popup = '<a class="rpxnow" onclick="return false;" href="';
		$popup .= $this->CodeIgniter->config->item('signin', 'RPX');
		$popup .= '?token_url=' . $this->token_url;
		$popup .= '">'.$message.'</a>';
		return $popup;
	  }
	  
	/**
	 * Embeded RPX Login
	 */
	function embed(){
		$embedURL = $this->CodeIgniter->config->item('embed', 'RPX');
		$embedURL .= '?token_url=' . $this->token_url;
		
		$embed = '';
		
		// Web browser check
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE){
			// The user is using IE
			$embed = '<iframe src="';
			$embed .= $embedURL;
			$embed .= '" scrolling="no" frameBorder="0" style="width:400px;height:240px;" />';
		}
		else{
			// The user is using a web browser
			$embed = '<object type="text/html" data="';
			$embed .= $embedURL;
			$embed .= '" style="width:400px;height:240px;" />';
		}
		return $embed;
	}
}
?>
