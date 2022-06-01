<?php 

function is_auth($proc = false): bool{

	if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {

	    $data = db()->query("
	        SELECT * 
	        FROM admin 
	        WHERE login = '" . $_SERVER['PHP_AUTH_USER'] . "' 
	            AND password = '" . md5($_SERVER['PHP_AUTH_PW']) . "'
	    ");

	    $data = $data->fetchALL();
	    
	    if(isset($data[0]))
	    	return true;
	}

	if($proc)
		proc_auth();

 	return false;
}

function proc_auth(): void{
 	header('HTTP/1.1 401 Unauthorized');
 	header('WWW-Authenticate: Basic realm="SITE"');
 	header('Content-Type: text/html; charset=UTF-8');

 	echo '<h1>401 Требуется авторизация</h1>';

 	exit();
}