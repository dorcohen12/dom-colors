<?php
	defined('INSITE') or die('No direct script access allowed');
	class Website{

        public $settings;

		public function __construct(){
			$this->settings = $this->GetSettings();
            if((isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) || $_SERVER['SERVER_PORT'] == 443){
				$url = 'https://';
			} else if(!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on'){
				$url = 'https://';
							} else if(!empty($_SERVER['HTTP_CF_VISITOR']) && json_decode($_SERVER['HTTP_CF_VISITOR'], true)['scheme'] == 'https'){
				$url = 'https://';																																																																		 
			} else{
				$url = 'http://';
			}
			$url .= $_SERVER['SERVER_NAME'] . htmlspecialchars($_SERVER['SCRIPT_NAME']);
			$parts = parse_url($url);
			if(substr($parts['path'], -1, 1) == '/')
				$parts['dirpath'] = $parts['path']; else
				$parts['dirpath'] = substr($parts['path'], 0, strrpos($parts['path'], '/') + 1);
			if((int)$_SERVER['SERVER_PORT'] <> 80 && (int)$_SERVER['SERVER_PORT'] <> 443)
				$this->settings->web_url = $parts['scheme'] . '://' . $parts['host'] . ':' . $_SERVER['SERVER_PORT'] . $parts['dirpath']; else
				$this->settings->web_url = $parts['scheme'] . '://' . $parts['host'] . $parts['dirpath'];
			unset($url);
			unset($parts);
			$this->settings->web_url = substr($this->settings->web_url, 0, -1);
		}
		private function GetSettings(){
			$path = BASE_DIR.'application/config/website_settings.json';
			if(file_exists($path)){
				$web_settings = file_get_contents($path);
				return json_decode($web_settings);
			}
			return null;
		}
		public function GetCachedImages(){
			$path = BASE_DIR.'application/config/images.json';
			if(file_exists($path)){
				$images = file_get_contents($path);
				return json_decode($images, true);
			}
			return false;
		}
		public function GetPersonalCachedImages($is_required_modify = []){
			$path = BASE_DIR.'application/config/images.json';
			if(file_exists($path)){
				$images = file_get_contents($path);
				$images = json_decode($images, true);
				if(array_key_exists(getUserIp(), $images)) {
					foreach($images[getUserIp()] as $key => $val) {
						$path = (new Image)->targetDir.getUserIp().'/'.$val['file'];
						if(!file_exists($path)) {
							$is_required_modify[] = $val['file'];
							unset($images[getUserIp()][$key]);
						}
					}
					if(count($is_required_modify) > 0) {
						$this->SaveWebImages($images);
					}
					return $images[getUserIp()];
				}
				return false;
			}
			return false;
		}
		public function SaveWebImages($data) {
			$path = BASE_DIR.'application/config/images.json';
			return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
		}

        public function AjaxToken() {
		    if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32).uniqid());
                $_SESSION['csrf_token_valid'] = strtotime('now +1 hour');
            } elseif(isset($_SESSION['csrf_token_valid']) && $_SESSION['csrf_token_valid'] < time()) {
                unset($_SESSION['csrf_token'], $_SESSION['csrf_token_valid']);
                $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32).uniqid());
                $_SESSION['csrf_token_valid'] = strtotime('now +1 hour');
            }
		}
		public function VerifyAjax() {
		    $headers = apache_request_headers();
			foreach($headers as $key => $val) {
				unset($headers[$key]);
				$key = strtolower($key);
				$headers[$key] = $val;
			}
		    return (isset($_SERVER['HTTP_HOST']) && strtolower($_SERVER['HTTP_HOST']) == strtolower(parse_url($this->settings->web_url, PHP_URL_HOST)) && isset($headers['token']) && isset($_SESSION['csrf_token']) && $headers['token'] == $_SESSION['csrf_token'] && isset($_SESSION['csrf_token_valid']) && $_SESSION['csrf_token_valid'] > time());
		}
	}