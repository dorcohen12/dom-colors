<?php
	session_start();
	require 'autoloader.php';
	$Website = new Website;

	$data = [];
    if($_SERVER['REQUEST_METHOD'] == 'POST' && $Website->VerifyAjax()) {
		$action = isset($_POST['action']) ? $_POST['action'] : '';
		$sub_action = isset($_POST['sub_action']) ? $_POST['sub_action'] : '';
		switch($action){
            case 'upload_image':
                $post_fields = [
                    'file_name' => isset($_POST['file_name']) ? trim($_POST['file_name']) : '',
                    'chunk_index' => isset($_POST['chunk_index']) ? trim($_POST['chunk_index']) : '',
                    'total_chunks' => isset($_POST['total_chunks']) ? trim($_POST['total_chunks']) : '',
                    'checksum' => isset($_POST['checksum']) ? trim($_POST['checksum']) : '',
                    'final_checksum' => isset($_POST['final_checksum']) ? trim($_POST['final_checksum']) : ''
                ];
                $file = isset($_FILES['chunk']) ? $_FILES['chunk'] : '';
                $data = (new Image)->processImage($post_fields, $file);
                break;
            default:
			$data['error'] = 'Invalid Request.';
		}
	}
	else{
		$data['error'] = 'Invalid request (6)';
		$data['type'] = 1;
	}
	echo json_encode($data);
