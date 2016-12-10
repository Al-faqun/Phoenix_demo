<?php
namespace Shinoa;

	use Shinoa\Model;
	use Shinoa\Exception\ModelException;
	use Shinoa\Exception\ViewException;
	class AdminView extends AbstractView	
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
		 * Возвращает текст нужной страницы.
		 * @param string $page
		 * @return string Текст страницы
		 */
		public function output($page)
		{
			ob_start();
			switch ($page) {
				case 'admin':
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


