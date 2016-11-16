<!-- скрипт получает текст цитат и публикует его на сайте -->

<?php for ($i = 0; $i < $count; $i++): 	//для каждой из выбранных цитат ?>
<?php switch ($i): ?>
<?php		case 0:
			//определяем расположение по горизонтали
			$num = 10 + mt_rand(0, 5);
			$left = "left: $num" . '%';
			//по вертикали
			$num = 10 + mt_rand(0, 5);
			$top = "top: $num" . '%'; 
			//и цвет
			$num = dechex(mt_rand(hexdec('000000'), hexdec('ffffff')));
			$color = 'color: #' . "$num"; 
			break;
		case 1:
			$num = 10 + mt_rand(0, 5);
			$left = "right: $num" . '%';
			
			$num = 25 + mt_rand(0, 5);
			$top = "top: $num" . '%'; 
			
			$num = dechex(mt_rand(hexdec('000000'), hexdec('ffffff')));
			$color = 'color: #' . "$num"; 
			break;
		case 2:
			$num = 10 + mt_rand(0, 5);
			$left = "left: $num" . '%';
			
			$num = 40 + mt_rand(0, 5);
			$top = "top: $num" . '%'; 	
			
			$num = dechex(mt_rand(hexdec('000000'), hexdec('ffffff')));
			$color = 'color: #' . "$num"; 
			break;
		case 3:
			$num = 10 + mt_rand(0, 5);
			$left = "right: $num" . '%';
			
			$num = 55 + mt_rand(0, 5);
			$top = "top: $num" . '%'; 
			
			$num = dechex(mt_rand(hexdec('000000'), hexdec('ffffff')));
			$color = 'color: #' . "$num"; 
			break;
		case 4:
			$num = 10 + mt_rand(0, 5);
			$left = "left: $num" . '%';
			
			$num = 70 + mt_rand(0, 5);
			$top = "top: $num" . '%'; 
			
			$num = dechex(mt_rand(hexdec('000000'), hexdec('ffffff')));
			$color = 'color: #' . "$num"; 
			break;
		case 5:
			$num = 10 + mt_rand(0, 5);
			$left = "right: $num" . '%';
			
			$num = 85 + mt_rand(0, 5);
			$top = "top: $num" . '%';
			
			$num = dechex(mt_rand(hexdec('000000'), hexdec('ffffff')));
			$color = 'color: #' . "$num"; 
			break;
		default: ?>
<?php endswitch; ?>
	
	<!-- постим цитату с нужными стилями -->
	<div style="position: absolute; 
					<?php echo $top; ?>;
					<?php echo $left; ?>;
					<?php if ($color_on === true) echo $color; ?>;">
		<p>«<?php echo htmlspecialchars($citations[$i], ENT_QUOTES, 'UTF-8'); ?>»</p>
	</div>
<?php endfor; ?>
