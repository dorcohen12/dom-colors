<?php
    /*== WEBSITE SETTINGS ===*/
	define('SHOW_ERRORS', true);
	define('WEB_PATH', 'dom-colors');	//Leave empty if website not in subdirectory
	define('DS', DIRECTORY_SEPARATOR);
	define('BASE_DIR', __DIR__.DS);
	define('TEMPLATE_NAME', 'default');
	define('TEMPLATE_DIR', BASE_DIR.'assets/'.TEMPLATE_NAME.'/');
    define('FILE_LIMIT', '1024');   // limit of file size, bytes, used for chunks.

	/*== DO NOT TOUCH ===*/
	if(!defined('INSITE')){
		define('INSITE', true);
	}