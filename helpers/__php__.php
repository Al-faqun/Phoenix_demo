<?php 
	if (file_exists(dirname(__DIR__) . '/__php__.php')) 
		include(dirname(__DIR__) . '/__php__.php');
	else define('DOC_ROOT', __DIR__);
?>