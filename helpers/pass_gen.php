<?php
/**
 * Этот скрипт позволяет создать хэш пароля при помощи новейшего алгоритма php
 * Этот хэш вручную добавляется в админку для сравнения с пользовательским вводом
 * Естественно, скрипт недоступен пользователям, только для настройки сайта.
 */

//вставляем сюда пароль, хэш которого хотим получить
$password = 'testpass';
//после первого запуска скрипта он выдаёт нам хэш, 
//вставляем его сюда, чтобы убедиться, что он верный и запомнить его
$hash = '$2y$10$Ik0g/6CU42Ktca/khmq3Le.08TioPZkeTr7FXaMPITNfPhsL/KlJq';
if (password_verify($password, $hash)) echo 'Hash verified!';
else {
	//если в скрипте указан не подходящий паролю хэш, то создаём верный хэш и показываем его
	$hash = password_hash($password, PASSWORD_DEFAULT);
	echo $hash;
}


?>