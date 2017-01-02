<!-- скрипт получает текст цитат и публикует его на сайте -->
<?php 
	//проверка, все ли переменные заданы перед запуском шаблона
	if (!isset($quotesPosAndColor))
		throw new Exception('No_var'); 
?>

<?php for ($i = 0; $i < $count; $i++): 	//для каждой из выбранных цитат ?>
	<!-- постим цитату с нужными стилями -->
	<div style="position: absolute; 
			<?php echo $quotesPosAndColor['top'][$i]; ?>;
			<?php echo $quotesPosAndColor['left'][$i]; ?>;
			<?php if ($color_on === true) echo $quotesPosAndColor['color'][$i]; ?>;
			">
		<p>«<?php echo htmlspecialchars($citations[$i], ENT_QUOTES, 'UTF-8'); ?>»</p>
	</div>
<?php endfor; ?>
