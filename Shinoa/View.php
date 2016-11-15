<?php
namespace Shinoa;

	use Shinoa\Model;
	use Shinoa\Exception\ModelException;
	use Shinoa\Exception\ViewException;
	class View	
	{
		/**
		 * Path to document root (absolute)
		 */
		private $DOC_ROOT;
		
		/**
		 * @var class Model 
		 */
		private $model;
		
		/**
		 *Path to templates' dir (absolute)
		 * 
		 * @var string 
		 */
		private $templateDir = '';
		
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
				$this->DOC_ROOT = $doc_root;
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
		public function output($page)
		{
			
			ob_start();
			switch ($page) {
				case 'main':
					//задаём переменные, необходимые в главно шаблоне
					//заголовок страницы 
					$header = $this->DOC_ROOT . '/Phoenix_demo/Private/tpl_header.php'; 
					if (!file_exists($header) || !is_file($header)) {
						throw new ViewException('Cannot output: header file not found.');
					}
					
					//чейнджлог
					$tpl_changes = $this->templateDir . '/tpl_changes.php';	
					if (!file_exists($tpl_changes) || !is_file($tpl_changes)) {
						throw new ViewException('Cannot output: changelog template not found.');
					}
					
					//сами цитаты
					$tpl_citations = $this->templateDir . '/tpl_citations.php';
					if (!file_exists($tpl_citations) || !is_file($tpl_citations)) {
						throw new ViewException('Cannot output: citations template not found.');
					}
					
					//необходимые данные из модели
					try {
						$color_on = $this->model->getColor();
						$changes = $this->model->getChanges();
						$citations = $this->model->getQuotes();
						$count = $this->model->getNumOfQuotes();
					} catch (ModelException $e) {
						throw new ViewException('Cannot output: ' . $e->getMessage());
					}
					
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

