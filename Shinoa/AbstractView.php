<?php
namespace Shinoa;

	use Shinoa\QuoteManager;
	use Shinoa\Exception\ViewException;
	abstract class AbstractView	
	{
		/**
		 * Path to document root (absolute)
		 */
		protected $doc_root;
		
		/**
		 * @var class QuoteManager 
		 */
		protected $model;
		
		/**
		 *Path to templates' dir (absolute)
		 * 
		 * @var string 
		 */
		protected $templateDir = '';
		
		/**
		 * Checks is file exists and throwes Exception otherwise.
		 * 
		 * @param string $absulutePath Path to file
		 * @param type $errMes Message for Exception
		 * @throws ViewException $errMes
		 */
		public static function check($absulutePath, $errMes)
		{
			if (!file_exists($absulutePath) || !is_file($absulutePath)) {
				throw new ViewException($errMes);
			}
		}
		
		/**
		 * 
		 * @param class_QuoteManager $model
		 * @param string $doc_root Path to document root (absolute) of web server
		 * @param string $templateDir Path to templates' dir (absolute)
		 */
		public function __construct(QuoteManager $model, $doc_root, $templateDir) 
		{
			try {
				$this->setRoot($doc_root);
				$this->setModel($model);
				$this->setTemplateDir($templateDir);
			} catch (ViewException $e) {
				throw new ViewException('Cannot construct view: ' . $e->getMessage());
			}
		}
		
		/**
		 * 
		 * @param String $doc_root
		 * @throws ViewException Not valid root
		 */
		public function setRoot($doc_root)
		{
			if (is_dir($doc_root)) {
				$this->doc_root = $doc_root;
			} else {
				throw new ViewException('Document root is not a valid directory!');
			}
		}
		
		/**
		 * 
		 * @param String $doc_root
		 * @throws ViewException Not valid root
		 */
		public function setModel($model)
		{
			if ($model instanceof QuoteManager) {
				$this->model = $model;
			}
			else throw new ViewException('Not valid model passed!');
		}
		
		/**
		 * 
		 * @param String $doc_root
		 * @throws ViewException Not valid root
		 */
		public function setTemplateDir($templateDir)
		{
			if (is_dir($templateDir)) {
				$this->templateDir = $templateDir;
			} else {
				throw new ViewException('Template dir is not a valid directory!');
			}
		}
		
		/**
		 * Возвращает текст нужной страницы.
		 * @param string $page
		 * @return string Текст страницы
		 */
		abstract public function output();
		
		/**
		 * Отсылает указанную страницу пользователю.
		 * @param string $page
		 */
		abstract public function render();
	}

