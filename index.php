<?php
	/**
	 * Скрипт контролирует информацию, которая отображается на странице сайта,
	 * а также обрабатывает действия пользователя
	 * Сайт показывает шесть случайных цитат человека с именем Phoenix,
	 * выбранных из заранее составленной БД mysql;
	 * цитаты расположены в случайных местах, со случайным цветом текста.
	 *
	 * @author Shinoa
	 */

	//буферизация
	ob_start();
	
	/**
	 * Простейший пример функции автозагрузки класса.
	 * 
	 * @param String $className 
	 */
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
		
		require DOC_ROOT . DIRECTORY_SEPARATOR . 'Phoenix_demo' . DIRECTORY_SEPARATOR . $fileName;
	}
	
	spl_autoload_register('autoload');
	
	/* раздел объявления констант */
	//этот скрипт ищет и задаёт файловый рут сайта (создаёт константу DOC_ROOT)
	include_once('__php__.php');
	//укорачиваем название классов из неймспейсов, они будут загружаться "сами"
	use Shinoa\Exception\ModelException;
	use Shinoa\Exception\ViewException;
	use Shinoa\Model;
	use Shinoa\View;
	use Shinoa\ErrorHelper;
	
	try {
		//создаём модель, представление, но не отображаем страницу
		$config = parse_ini_file(DOC_ROOT . '/Phoenix_demo/ini/config.ini');
		$model = new Model($config, DOC_ROOT);
		$view = new View($model, DOC_ROOT, DOC_ROOT . '/Phoenix_demo/templates');
	} catch (ModelException $e) {
		$errorMes = 'Cannot create model: ' . $e->getMessage();
		$whereToRedirect = ' ';
		$errorHelper = new ErrorHelper(DOC_ROOT . '/Phoenix_demo/templates');
		$errorHelper->renderErrorPageAndExit($errorMes, $whereToRedirect);
		
	} catch (ViewException $e) {
		$errorMes = 'Cannot create view: ' . $e->getMessage();
		$whereToRedirect = ' ';
		$errorHelper = new ErrorHelper(DOC_ROOT . '/Phoenix_demo/templates');
		$errorHelper->renderErrorPageAndExit($errorMes, $whereToRedirect);
		
	} catch (Exception $e) {
		$errorMes = 'General exceptional situation: ' . $e->getMessage();
		$whereToRedirect = ' ';
		$errorHelper = new ErrorHelper(DOC_ROOT . '/Phoenix_demo/templates');
		$errorHelper->renderErrorPageAndExit($errorMes, $whereToRedirect);
	}
	
	try {
		//участок кода контроллера 
		//проверяем пользовательский ввод и отправляем его в Модель,
		//оттуда данные берёт Представление
		//кнопка цвета цитат
		if (isset($_GET['color'])) {
			$color_on = ($_GET['color'] === '0') ? false : true;
			$model->setColor($color_on);
		}
	
		//перемещаем пользователя на страницу админки
		if (isset($_GET['new_quote'])) {
		    header('Location: ' . "/Phoenix_demo/Admin/", true);         
		}
	
		//отсылаем страницу пользователю
		$view->render('main');
	} catch (Exception $e) {
		$errorMes = 'General exceptional situation: ' . $e->getMessage();
		$whereToRedirect = '';
		$errorHelper = new ErrorHelper(DOC_ROOT . '/Phoenix_demo/templates');
		$errorHelper->renderErrorPageAndExit($errorMes, $whereToRedirect);
	}
	ob_end_flush();
