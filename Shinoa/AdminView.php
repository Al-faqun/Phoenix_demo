<?php
namespace Shinoa;

	use Shinoa\QuoteManager;
	use Shinoa\Exception\ViewException;
	class AdminView extends AbstractView	
	{	
		/**
		 * 
		 * @param class_QuoteManager $model
		 * @param string $doc_root Path to document root (absolute) of web server
		 * @param string $templateDir Path to templates' dir (absolute)
		 */
		public function __construct(QuoteManager $model, $doc_root, $templateDir) 
		{
			parent::__construct($model, $doc_root, $templateDir);
		}
		
		/**
		 * Возвращает текст нужной страницы.
		 * @param string $page
		 * @return string Текст страницы
		 */
		public function output()
		{
			ob_start();
			Helpers::sessionTrueStart();
			
			//вызывает общий шаблон страницы
			$outputFile = $this->templateDir . '/tpl_main.php';
			$errMes = 'Cannot output file';
			self::check($outputFile, $errMes);
			include $outputFile;
			
			//удаляем все данные, записанные в сессию
			session_destroy();
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


