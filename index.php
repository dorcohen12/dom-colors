<?php
	ob_start();
	session_start();
	
	require 'autoloader.php';

	if(!extension_loaded('gd')) {
		exit('GD Extension is required');
	}
    
	$Website = new Website;
    $Website->AjaxToken();

	$path = preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);
	if(!empty(WEB_PATH)){
		$path = str_replace('/'.WEB_PATH.'/', '', $path);
	}
	$paths = explode('/', $path);
	$paths = array_filter($paths);
	$paths = array_values($paths);
	if(!isset($paths[0])){
		$paths[0] = 'home';
	}
	$folder = '';
	$page = $paths[0];
	if(file_exists('pages'.$folder.'/'.$page.'.php')){
        require 'pages'.$folder.'/'.$page.'.php';
	}
	else{
		$page = 'Unknown page';
		require 'pages/404.php';
	}
	ob_end_flush();
