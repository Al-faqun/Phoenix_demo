<?php
namespace Shinoa;

	use Shinoa\Model;
	use Shinoa\Exception\ModelException;
	use Shinoa\Exception\ViewException;
	abstract class AbstractView	
	{
		/**
		 * Path to document root (absolute)
		 */
		protected $doc_root;
		
		/**
		 * @var class Model 
		 */
		protected $model;
		
		/**
		 *Path to templates' dir (absolute)
		 * 
		 * @var string 
		 */
		protected $templateDir = '';
		
		/**
		 * 
		 * @param class_Model $model
		 * @param string $doc_root Path to document root (absolute) of web server
		 * @param string $templateDir Path to templates' dir (absolute)
		 */
		public function __construct(Model $model, $doc_root, $templateDir) 
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
				throw new DatabaseException('Document root is not a valid directory!');
			}
		}
		
		/**
		 * 
		 * @param String $doc_root
		 * @throws ViewException Not valid root
		 */
		public function setModel($model)
		{
			if ($model instanceof Model) {
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
				throw new DatabaseException('Template dir is not a valid directory!');
			}
		}
		
		/**
		 * Возвращает текст нужной страницы.
		 * @param string $page
		 * @return string Текст страницы
		 */
		abstract public function output($page);
		
		/**
		 * Отсылает указанную страницу пользователю.
		 * @param string $page
		 */
		abstract public function render($page);
	}

