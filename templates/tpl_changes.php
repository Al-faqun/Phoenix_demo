<!-- отображает чейндж-лог -->
<?php 
	//проверка, все ли переменные заданы перед запуском шаблона
	if (!isset($changes))
		throw new Exception('No_var'); 
?>

<input type="checkbox" id="hd-1" class="hide"/>
    <label for="hd-1">Changelog</label>
    <div>
	<textarea rows="5" cols="60" readonly><?php
		foreach ($changes as $line): 
		if (!empty($line)) echo $line . PHP_EOL; 
        endforeach; 
	?></textarea>
    </div>