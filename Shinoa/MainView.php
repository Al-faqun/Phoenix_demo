<?php
namespace Shinoa;

	use Shinoa\Model;
	use Shinoa\Exception\ModelException;
	use Shinoa\Exception\ViewException;
	class MainView extends AbstractView	
	{
		/**
		 * 
		 * @param class_Model $model
		 * @param string $doc_root Path to document root (absolute) of web server
		 * @param string $templateDir Path to templates' dir (absolute)
		 */
		public function __construct(Model $model, $doc_root, $templateDir) 
		{
			parent::__construct($model, $doc_root, $templateDir);
		}

		/**
		 * Procedure places ready css code of place and color for a text quote
		 * 
		 * @param Int $left_offset
		 * @param Int $top_offset
		 * @param String by reference $top_outcome
		 * @param String by reference $left_outcome
		 * @param String by reference $color_outcome
		 */
		public static function randPosAndColor($left_offset, $top_offset, 
				                          &$left_outcome, &$top_outcome, &$color_outcome)
		{
			//определяем расположение по горизонтали
			$num = $left_offset + mt_rand(0, 5);
			$left_outcome = "left: $num" . '%';
			//по вертикали
			$num = $top_offset + mt_rand(0, 5);
			$top_outcome = "top: $num" . '%'; 
			//и цвет
			$num = dechex(mt_rand(hexdec('000000'), hexdec('ffffff')));
			$color_outcome = 'color: #' . "$num"; 
		}
		
		/**
		 * 
		 * @return Array Array of tops, lefts and colors of every quote; 
		 * use 'top', 'left' and 'color' as first index, number of quote as second index.
		 */
		public static function quotesPosAndColor($count) 
		{
			$quotesPosAndColor = array();
			for ($i = 0; $i < $count; $i++) { 	//для каждой из выбранных цитат
				$top = 0; $left = ''; $color = '';
				switch ($i) {
					case 0:
						self::randPosAndColor(10, 10, $top, $left, $color);
						break;
					case 1:
						self::randPosAndColor(50, 25, $top, $left, $color);
						break;
					case 2:
						self::randPosAndColor(10, 40, $top, $left, $color);
						break;
					case 3:
						self::randPosAndColor(50, 55, $top, $left, $color);
						break;
					case 4:
						self::randPosAndColor(10, 70, $top, $left, $color);
						break;
					case 5:
						self::randPosAndColor(50, 85, $top, $left, $color);
						break;
					default: 
				}
				$quotesPosAndColor['top'][$i] = $top;
				$quotesPosAndColor['left'][$i] = $left; 
				$quotesPosAndColor['color'][$i] = $color; 
			}
			return $quotesPosAndColor;
		}
		
		/**
		 * Возвращает текст нужной страницы.
		 * @param string $page
		 * @return string Текст страницы
		 */
		public function output($page)
		{
			
			ob_start();
			switch ($page) {
				case 'main':
					//задаём переменные, необходимые в главноv шаблоне
					//необходимые данные из модели
					try {
						$color_on = $this->model->getColor();
						$changes = $this->model->getChanges();
						$citations = $this->model->getQuotes();
						$count = $this->model->getNumOfQuotes();
					} catch (ModelException $e) {
						throw new ViewException('Cannot output: ' . $e->getMessage());
					}
					
					//заголовок страницы 
					$header = $this->doc_root . '/Phoenix_demo/Private/tpl_header.php'; 
					if (!file_exists($header) || !is_file($header)) {
						throw new ViewException('Cannot output: header file not found.');
					}
					
					//чейнджлог
					$tpl_changes = $this->templateDir . '/tpl_changes.php';	
					if (!file_exists($tpl_changes) || !is_file($tpl_changes)) {
						throw new ViewException('Cannot output: changelog template not found.');
					}
					
					//счётчик посещений
					$tpl_counter = $this->doc_root . '/Phoenix_demo/Private/counter.php';
					if (!file_exists($tpl_counter) || !is_file($tpl_counter)) {
						throw new ViewException('Cannot output: counter template not found.');
					}
					
					//сами цитаты
					$tpl_citations = $this->templateDir . '/tpl_citations.php';
					if (!file_exists($tpl_citations) || !is_file($tpl_citations)) {
						throw new ViewException('Cannot output: citations template not found.');
					}
					
					//css для tpl_citations(да, я в курсе что frontend лучше через JS, но я ещё не знаю его :_:)
					$quotesPosAndColor = self::quotesPosAndColor($count);
					
					//вызывает общий шаблон страницы
					$outputFile = $this->templateDir . '/tpl_main.php';
					if (file_exists($outputFile) && is_file($outputFile)) {
						include $outputFile;
					} else throw new ViewException('Cannot output file');
					
					break;
				default:
					break;
			} 
			return ob_get_clean();			
		}
		
		/**
		 * Отсылает указанную страницу пользователю.
		 * @param string $page
		 */
		public function render($page) 
		{
			header('Content-type: text/html; charset=utf-8');
			$contents = $this->output($page);
			echo $contents;
		}
	}


