<?php
	/**
	 * Скрипт контролирует информацию, которая отображается на странице сайта,
	 * а также обрабатывает действия пользователя
	 * Сайт показывает шесть случайных цитат человека с именем Phoenix,
	 * выбранных из заранее составленной БД mysql;
	 * цитаты расположены в случайных местах, со случайным цветом текста.
	 *
	 * @author Shinoa
	 */

	//буферизация
	ob_start();
	
	/* раздел объявления */
	//укорачиваем название классов из неймспейсов, они будут загружаться "сами"
	use \Shinoa\QuoteManager;
	use \Shinoa\MainView;
	use \Shinoa\ErrorHelper;
	use \Shinoa\Exception\ModelException;
	use \Shinoa\Exception\ViewException;
	use \Shinoa\Exception\LoaderException;
	use \Shinoa\Exception\ExReporter;
	
	Class MainLoader 
	{
		private $docRoot = '';
		private $model = null;
		private $view = null;
		
		/**
		 * Простейший пример функции автозагрузки класса.
		 * 
		 * @param String $className 
		 */		
		private function autoload($className) 
		{
			$className = ltrim($className, '\\');
			$fileName = '';
			$namespace = '';
			if ($lastNsPos = strrpos($className, '\\')) {
				$namespace = substr($className, 0, $lastNsPos);
				$className = substr($className, $lastNsPos + 1);
				$fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
			}
			$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		
			require $this->docRoot . DIRECTORY_SEPARATOR . 'Phoenix_demo' . DIRECTORY_SEPARATOR . $fileName;
		}	
		
		/**
		 * Document root
		 * 
		 * @param String $root Valid path, without ending '/'
		 * @throws LoaderException Not valid directory
		 */
		protected function setRoot($root) 
		{
			if (is_dir($root)) {
				$this->docRoot = $root;
			} else throw new LoaderException('Cannot set root: not valid directory');
		}
		
		/**
		 * 
		 * @param Array $config Parsed ini config
		 */
		private function loadModel($config)
		{
			$this->model = new QuoteManager($config);
			$this->model->setChangeLogPath($this->docRoot . '/Phoenix_demo/changelog.txt');
		}
		
		/**
		 * 
		 * @param String $templateDirRel Relative to document root, starting with '/'
		 * @throws LoaderException
		 */
		private function loadView($templateDirRel)
		{
			if (($this->model === null) || !($this->model instanceof QuoteManager)) {
				throw new LoaderException('Model is not set before loading view');
			} 
			
			$templateDirAbs = $this->docRoot . $templateDirRel;
			if (!is_dir($templateDirAbs)) {
				throw new LoaderException('Templates path is not a valid dir');
			}
			
			$this->view = new MainView($this->model, $this->docRoot, $templateDirAbs);
		}
		
		private function checkInput()
		{
			//участок кода контроллера 
			//проверяем пользовательский ввод и отправляем его в Модель,
			//оттуда данные берёт Представление
			//кнопка цвета цитат
			if (isset($_GET['color'])) {
				$color_on = !($_GET['color'] === '0');
				$this->model->setColor($color_on);
			}
	
			//перемещаем пользователя на страницу админки
			if (isset($_GET['new_quote'])) {
			    header('Location: ' . "/Phoenix_demo/Admin/", true);         
			}
		}
		
		public function main()
		{
			spl_autoload_register(array($this, 'autoload'));
			$root = 'D:\USR\apache\htdocs\s2.localhost';
			$this->setRoot($root);
			
			try {
				$config_path = 
				    (file_exists($this->docRoot . '/Phoenix_demo/ini/config_test.ini')) 
					    ? $this->docRoot . '/Phoenix_demo/ini/config_test.ini'
					    : $this->docRoot . '/Phoenix_demo/ini/config_mock.ini';
				if (!file_exists($config_path)) {
					throw new LoaderException('Nonexistent config file');
				}
				
				$config = parse_ini_file($config_path);
				$this->loadModel($config);
				$this->loadView('/Phoenix_demo/templates');
				
				$this->checkInput();
				//отсылаем страницу пользователю
				$this->view->render();
				
			} catch (ModelException $e) {
				$errorMes = 'Cannot create model: ' . ExReporter::HTML($e);
				$whereToRedirect = ' ';
				$errorHelper = new ErrorHelper($this->docRoot . '/Phoenix_demo/templates');
				$errorHelper->renderErrorPageAndExit($errorMes, $whereToRedirect);
		
			} catch (ViewException $e) {
				$errorMes = 'Cannot create view: ' . ExReporter::HTML($e);
				$whereToRedirect = ' ';
				$errorHelper = new ErrorHelper($this->docRoot . '/Phoenix_demo/templates');
				$errorHelper->renderErrorPageAndExit($errorMes, $whereToRedirect);
		
			} catch (Exception $e) {
				$errorMes = 'General exceptional situation: ' . ExReporter::HTML($e);
				$whereToRedirect = ' ';
				$errorHelper = new ErrorHelper($this->docRoot . '/Phoenix_demo/templates');
				$errorHelper->renderErrorPageAndExit($errorMes, $whereToRedirect);
			}
		}
	}
	
	$loader = new MainLoader();
	$loader->main();
	ob_end_flush();