<?php
namespace Shinoa\Tests;

function autoload($className) 
{
	$className = ltrim($className, '\\');
	$fileName = '';
	$namespace = '';
	if ($lastNsPos = strrpos($className, '\\')) {
		$namespace = substr($className, 0, $lastNsPos);
		$className = substr($className, $lastNsPos + 1);
		$fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
	}
	$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
	
	$path = \Shinoa\Tests\DOC_ROOT . DIRECTORY_SEPARATOR . 'Phoenix_demo' . DIRECTORY_SEPARATOR . $fileName;
	if (file_exists($path)) {
		require $path;
	}
}	

spl_autoload_register('\Shinoa\Tests\autoload');