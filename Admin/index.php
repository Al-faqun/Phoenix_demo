<?php
	/**
	 * Эта "админ-панель" позволяет добавлять цитаты в БД.
	 */

	include_once('__php__.php');
	include_once(DOC_ROOT . '/Phoenix_demo/helpers/helpers.inc.php');
	//сессия нужна для сохранения состояния "лог-ина",
	//а также позволяет передавать сообщения между страницами.
	session_true_start();

    //если введён пароль или текст цитаты для добавления
	//этот код необходим  для реализации паттерна 'post - redirect - get'
	if (isset($_SESSION['pswd']) OR isset($_SESSION['textarea'])) {
		include DOC_ROOT . '/Phoenix_demo/Admin/tpl_main.php';
		exit();
	}
	
	//если не сказано обратное, пользователь считается не-авторизированным
	$_SESSION['verified'] = 'false';
	//очищаем временную переменную, которая может хранить ненужное сообщение
	unset($_SESSION['output']);
	//загружаем страницу первый раз, все последующие запросы идут к process.php
	include DOC_ROOT . '/Phoenix_demo/Admin/tpl_main.php';
?>