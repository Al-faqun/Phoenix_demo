<?php 
	
	if (!isset($header)
			OR
		!isset($tpl_changes)  
			OR 
		!isset($color_on) 
			OR 
		!isset($count)
			OR
		!isset($tpl_citations)) 
		throw new Exception('No_var'); 
?>

<!DOCTYPE html>
<!-- отображает элементы главной страницы сайта -->
<html>
	<?php include_once($header); ?>
	<body>
		<!-- чейнджлог -->
		<div class="right-top">
			<?php include $tpl_changes; ?>
		</div>

		<div class="btn" id="disable_colors">
			<?php if ($color_on === true): ?>
			<form action="" method="GET">
				<input type="hidden" name="color" value="0"/>
				<input type="submit" value="Отключить цвета"/>
			</form>
			<?php else: ?>
				<form action="" method="GET">
					<input type="hidden" name="color" value="1"/>
					<input type="submit" value="Включить цвета"/>
				</form>
			<?php endif; ?>
		</div>

		<div class="btn" id="new_quote">
			<form action="" method="GET">
				<input type="hidden" name="new_quote"/>
				<input type="submit" value="Добавить новую цитату"/>
			</form>
		</div>
		
		<!-- текст цитат -->
		<div id="quotes">
		<?php include $tpl_citations; ?>
		</div>

		<div class="right-bottom">
		    <?php include_once(DOC_ROOT . '/Phoenix_demo/Private/counter.php'); ?>
		</div>
	</body>
</html>