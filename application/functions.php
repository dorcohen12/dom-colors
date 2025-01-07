<?php
	defined('INSITE') or die('No direct script access allowed');
	function Message($type, $text){
		$html = '';
		if($type == 'success'){
			$html .= '<div class="alert alert-success"><strong>Success!</strong>';
		}
		elseif($type == 'info'){
			$html .= '<div class="alert alert-info"><strong>Info!</strong>';
		}
		elseif($type == 'warning'){
			$html .= '<div class="alert alert-warning"><strong>Warning!</strong>';
		}
		else{
			$html .= '<div class="alert alert-danger"><strong>Error!</strong>';
		}
		$html .= ' '.$text.'</div>';
		echo $html;
	}
	function CheckFields($required_fields, $data){
		$data = array_filter($data, 'strlen');
		foreach($required_fields as $key){
			if(!array_key_exists($key, $data)){
				return false;
			}
		}
		return true;
	}
	function getUserIP() {
		if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
				$_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
				$_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
		}
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote  = $_SERVER['REMOTE_ADDR'];

		if(filter_var($client, FILTER_VALIDATE_IP)) {
			$ip = $client;
		} elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
			$ip = $forward;
		} else {
			$ip = $remote;
		}
		return $ip;
	}