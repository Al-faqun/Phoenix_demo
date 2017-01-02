<!-- Шаблон 'отображения' страницы админки -->

<!DOCTYPE html>
<html>
	<head>
		<title>Отправить цитату</title>
		<meta charset="utf-8">
	</head>
	<body>
		<!-- опциональный текст для пользователя, результат предыдущего действия -->
		<?php if (isset($_SESSION['output'])): ?>
			<p style="color: #DF63E6"><?php echo $_SESSION['output']; ?></p>
			<?php endif; ?>
			
		<!-- если мы попали на страницу после того, 
		      как пользователь ввёл верный пароль -->
		<?php if(isset($_SESSION['verified']) && ($_SESSION['verified'] === 'true')): ?>
			<form action="" method="POST">
				<label for="fe-1">Отправить цитату в базу данных</label>
				<textarea name="textarea" id="fe-1" rows="4" cols="70"></textarea></br>
				<label for="fe-2">Выберите категорию, в которую хотите отправить цитату:</label>
				<select id="fe-2" name="select">
					<option value="Dream">Сны</option>
					<option value="Animals">Живность</option>
					<option value="Hugs">Обнимания</option>
					<option value="Fury">Гнев</option>
					<option value="Other">Другое</option>
				</select>
				<input type="Submit" value="Отправить"/>
			</form>
		
		<!-- если пароль не введён  или введён не верно, просим ввести -->
		<?php else: ?>
			<form action="" method="POST">
				<label for="pswd">Введите пароль:</label>
				<input type="password" id="pswd" name="pswd"/>
				<input type="Submit" value="Войти">
			</form>
		<?php endif;?>
		</br>
		<form action="" method="POST">
			<input type="Submit" name="back" value="Вернуться на главную страницу"/>
		</form>
	</body>
</html>
