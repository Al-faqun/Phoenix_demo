<?php
namespace Shinoa;
	include_once('__php__.php');
	include_once 'D:\USR\apache\htdocs\s2.localhost\Phoenix_demo\classes\Model.php';
	class Controller 
	{
		/**
		 *
		 * @var class_Model  
		 */
		private $model;
		
		/**
		 * 
		 * @param class_Model $model
		 */
		public function __construct(Model $model) 
		{
			$this->model = $model;
		}
		
		/**
		 * Returns whether there is some variable in $_GET or $_POST.
		 * @return boolean 
		 */
		public function pending()
		{
			if (empty($_GET) AND empty($_POST)) 
				return false;
			else return true;
		}
		

		public function exists($type, $key) 
		{
			if (strtolower($type) === 'get') {
				return isset($_GET($key));
			}
			else 
			if (strtolower($type) === 'post') {
				return isset($_POST($key));
			}
			else return 0;
		}

		public function getValue($type, $key)
		{
			if (strtolower($type) === 'get') {
				return $_GET($key);
			}
			else 
			if (strtolower($type) === 'post') {
				return $_POST($key);
			}
			else return false;
		}
	}
	
	$config = parse_ini_file(DOC_ROOT . '/Phoenix_demo/ini/config.ini');
	$model = new Model($config, DOC_ROOT);
	$controller = new Controller($model);
	if ($controller->pending() === true) {
		//обрабатывает нажатие кнопки "новая цитата"
		if ($controller->exists('get', 'new_quote')) {
			//перемещает пользователя на страницу админки
			header('Location: ' . "/Phoenix_demo/Admin/", true, 303);
		}
		
		// Обрабатывает  нажатие кнопки "Убрать цвет" 
		if ($controller->exists('get', 'color')) {
			if ($controller->getValue('get', 'color') === '0') {
				$controller->model->setColor(false);
			} 
			else $controller->model->setColor(true);
		}		
	}
	
     // Обрабатывает  нажатие кнопки "Убрать цвет"  
    if (@$_GET['color'] === '0') $color_on = false; 
     else $color_on = true;
	
if ((isset($_GET['action'])) && !empty($_GET['action'])) 
	{
		$controller->{$_GET['action']}();
	}