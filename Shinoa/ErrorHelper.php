<?php

namespace Shinoa;

/**
 * Description of ErrorHelper
 *
 * @author Shinoa
 */
class ErrorHelper {
	private $templateDir = '';
	public function __construct($templateDir) {
		if (is_string($templateDir)) {
			$this->templateDir = $templateDir;
		}
	}
	public function renderErrorPageAndExit($errorMes, $whereToRedirect) 
	{
		header('Content-type: text/html; charset=utf-8');
		$error = $errorMes;
		$form_action = $whereToRedirect;
		$form_method = 'post';
		$input_value = 'Вернуться';
		include $this->templateDir . '/error.html.php';	
		exit();
	}	
}
