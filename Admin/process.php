<?php
	/**
	 * Скрипт обрабатывает запросы от пользователя со страницы
	 */

	include_once('__php__.php');
	include_once(DOC_ROOT . '/Phoenix_demo/helpers.inc.php');
	session_true_start();

	//подключаемся к БД
	$config = parse_ini_file(DOC_ROOT . '/Phoenix_demo/ini/config.ini');
	include DOC_ROOT . '/Phoenix_demo/classes/Connection.php';
	
    //если пользователь отправил форму с цитатой, записываем данные в сессию,
	//чтобы не терять во время перехода между страницами
	//пользовательский ввод проверяется уже при использовании сессионных переменных
	if (isset($_POST['textarea'])) 
	{
		$_SESSION['textarea'] = $_POST['textarea'];
		if (isset($_POST['select'])) 
		{
			$_SESSION['select'] = $_POST['select'];
		}
        //редирект на ту же страницу, заодно очищает массив $_POST
        //здесь и далее при помощи редиректов реализуем шаблон post - redirect - get, благодаря которому F5 не отсылает данные заново
		header('Location: ' . "/Phoenix_demo/Admin/process.php", true, 303);
		exit();
	}

    //после редиректа: обрабатываем данные из формы, добавляем цитату в базу данных
	if (isset($_SESSION['textarea']) &&(isset($_SESSION['select'])) ) 
	{
		$quoteText = $_SESSION['textarea'];
		$model->insertQuote($quoteText);
        //добавляем в базу данных запись о том, какой категории принадлежит цитата
		$select = $_SESSION['select'];
		$model->insertQuoteCategory($select); 
		//эта переменная отображается на страничке после редиректа (статус)
		$_SESSION['output'] = 'Успешно добавили цитату.';    
		header('Location: ' . "/Phoenix_demo/Admin/", true, 303);
		exit;
	}

    //каждый раз когда хотим добавить цитату, сперва проверяем пароль на правильность
	if (isset($_POST['pswd'])) 
	{
		$_SESSION['pswd'] = $_POST['pswd'];
		include DOC_ROOT . '/Phoenix_demo/Private/pswd_check.php';
		//возвращаемся к главному скрипту админки
		header('Location: ' . "/Phoenix_demo/Admin/", true, 303);    
		exit;
	}

    //если пользователь хочет вернуться на главную страницу
	if (isset($_POST['back'])) 
	{
		header('Location: ' . "/Phoenix_demo/",true,303);
		exit;
	}
?>