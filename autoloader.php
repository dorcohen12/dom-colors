<?php
	ini_set('memory_limit', '1G');
	require 'config.php';
	date_default_timezone_set("Asia/Jerusalem");
	switch(SHOW_ERRORS){
		case 0:
			ini_set('display_errors', 0);
			ini_set('display_startup_errors', 0);
			error_reporting(0);
			break;
		case 1:
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
			break;
	}
	
	require 'application/functions.php';
	spl_autoload_register(function($class){
        $path = 'application/classes/';
        $path .= str_replace('\\', '/', $class).'.php';
		require $path;	
    });

    if (!is_writable(session_save_path())) {
        exit('Session path "'.session_save_path().'" is not writable for PHP!'); 
    }