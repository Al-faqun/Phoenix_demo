<?php
namespace Shinoa;

	use Shinoa\QuoteManager;
	use Shinoa\Exception\ModelException;
	use Shinoa\Exception\ViewException;
	class MainView extends AbstractView	
	{
		/**
		 * 
		 * @param classQuoteManager $model
		 * @param string $doc_root Path to document root (absolute) of web server
		 * @param string $templateDir Path to templates' dir (absolute)
		 */
		public function __construct(QuoteManager $model, $doc_root, $templateDir) 
		{
			parent::__construct($model, $doc_root, $templateDir);
		}

		/**
		 * Procedure places ready css code of place and color for a text quote
		 * 
		 * @param Int $left_offset at page
		 * @param Int $top_offset at page
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
		public function output()
		{
			
			ob_start();
			//задаём переменные, необходимые в главноv шаблоне
			//необходимые данные из модели
			try {
				$color_on = $this->model->getColor();
				$changes = $this->model->getChangeLog();
				if ($changes === false) $changes = 'Changelog not found';
				$citations = $this->model->getQuotes();
				$count = $this->model->getNumOfQuotes();
			} catch (ModelException $e) {
				throw new ViewException('Cannot output: ' . $e->getMessage());
			}
					
			//заголовок страницы 
			$header = $this->doc_root . '/Phoenix_demo/Private/tpl_header.php';
			$errMes = 'Cannot output: header file not found.';
			self::check($header, $errMes);
					
			//чейнджлог
			$tpl_changes = $this->templateDir . '/tpl_changes.php';
			$errMes = 'Cannot output: changelog template not found.';
			self::check($tpl_changes, $errMes);
					
			//счётчик посещений
			$tpl_counter = $this->doc_root . '/Phoenix_demo/Private/counter.php';
			$errMes = 'Cannot output: counter template not found.';
			self::check($tpl_counter, $errMes);
					
			//сами цитаты
			$tpl_citations = $this->templateDir . '/tpl_citations.php';
			$errMes = 'Cannot output: citations template not found.';
			self::check($tpl_citations, $errMes);
					
			//css для tpl_citations(да, я в курсе что лучше через JS, но я пока за него не взялся :_:)
			$quotesPosAndColor = self::quotesPosAndColor($count);
					
			//вызывает общий шаблон страницы
			$outputFile = $this->templateDir . '/tpl_main.php';
			$errMes = 'Cannot output file';
			self::check($outputFile, $errMes);
			include $outputFile;
			
			return ob_get_clean();			
		}
		
		/**
		 * Отсылает указанную страницу пользователю.
		 * @param string $page
		 */
		public function render() 
		{
			header('Content-type: text/html; charset=utf-8');
			$contents = $this->output();
			echo $contents;
		}
	}


