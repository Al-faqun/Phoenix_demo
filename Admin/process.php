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
	if (isset($_SESSION['textarea'])) 
	{
		$quote_text = $mysqli->real_escape_string($_SESSION['textarea']);
		$sql = "INSERT INTO `quotes` SET
		     `quote`='$quote_text'";
		if (!$mysqli->query($sql))
		{
			$error = 'Error inserting quote: ' . $mysqli->error;
			include DOC_ROOT . '/Phoenix_demo/templates/error.html.php';
			exit();
		}
        //добавляем в базу данных запись о том, какой категории принадлежит цитата
		if (isset($_SESSION['select'])) 
		{
			$select = $mysqli->real_escape_string($_SESSION['select']);
			$quote_id = $mysqli->insert_id;
			$sql = "SELECT `id` FROM `categories`
			       WHERE `name`='$select' LIMIT 1";
			if ($result = $mysqli->query($sql))
			{
				$category_id= mysqli_fetch_row($result)[0];
				mysqli_free_result($result);
			}
			else 
			{
				$error = 'Error selecting quote category id: ' . $mysqli->error;
				include DOC_ROOT . '/Phoenix_demo/templates/error.html.php';
				exit();
			}

			$sql = "INSERT INTO `quote_category` SET
			       `quote_id`=$quote_id,
			       `category_id`=$category_id";
			if (!$mysqli->query($sql))
			{
				$error = 'Error inserting quote-category record: ' . $mysqli->error;
				include DOC_ROOT . '/Phoenix_demo/templates/error.html.php';
				exit();
			}
		}
		$_SESSION['output'] = 'Успешно добавили цитату.';    //эта переменная отображается на страничке после редиректа (статус)
		header('Location: ' . "/Phoenix_demo/Admin/",true,303);
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