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
	use Shinoa\Model;
	use Shinoa\MainView;
	use Shinoa\ErrorHelper;
	use Shinoa\Exception\FileException;
	use Shinoa\Exception\ModelException;
	use Shinoa\Exception\ViewException;
	use Shinoa\Exception\LoaderException;
	
	Class Loader 
	{
		private $doc_root = '';
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
		
			require $this->doc_root . DIRECTORY_SEPARATOR . 'Phoenix_demo' . DIRECTORY_SEPARATOR . $fileName;
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
				$this->doc_root = $root;
			} else throw new LoaderException('Cannot set root: not valid directory');
		}
		
		/**
		 * 
		 * @param Array $config Parsed ini config
		 */
		private function loadModel($config)
		{
			$this->model = new Model($config);
		}
		
		/**
		 * 
		 * @param String $templateDirRel Relative to document root, starting with '/'
		 * @throws LoaderException
		 */
		private function loadView($templateDirRel)
		{
			if (($this->model === null) || !($this->model instanceof Model)) {
				throw new LoaderException('Model is not set before loading view');
			} 
			
			$templateDirAbs = $this->doc_root . $templateDirRel;
			if (!is_dir($templateDirAbs)) {
				throw new LoaderException('Templates path is not a valid dir');
			}
			
			$this->view = new MainView($this->model, $this->doc_root, $templateDirAbs);
		}
		
		private function checkInput()
		{
			//участок кода контроллера 
			//проверяем пользовательский ввод и отправляем его в Модель,
			//оттуда данные берёт Представление
			//кнопка цвета цитат
			if (isset($_GET['color'])) {
				$color_on = ($_GET['color'] === '0') ? false : true;
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
				$this->checkInput();
				
				$config_path = 
				    (file_exists($this->doc_root . '/Phoenix_demo/ini/config_test.ini')) 
					    ? $this->doc_root . '/Phoenix_demo/ini/config_test.ini'
					    : $this->doc_root . '/Phoenix_demo/ini/config_mock.ini';
				if (!file_exists($config_path)) {
					throw new LoaderException('Nonexistent config file');
				}
				
				$config = parse_ini_file($config_path);
				$this->loadModel($config);
				$this->loadView('/Phoenix_demo/templates');
				
				//отсылаем страницу пользователю
				$this->view->render('main');
			} catch (ModelException $e) {
				$errorMes = 'Cannot create model: ' . $e->getMessage();
				$whereToRedirect = ' ';
				$errorHelper = new ErrorHelper(DOC_ROOT . '/Phoenix_demo/templates');
				$errorHelper->renderErrorPageAndExit($errorMes, $whereToRedirect);
		
			} catch (ViewException $e) {
				$errorMes = 'Cannot create view: ' . $e->getMessage();
				$whereToRedirect = ' ';
				$errorHelper = new ErrorHelper(DOC_ROOT . '/Phoenix_demo/templates');
				$errorHelper->renderErrorPageAndExit($errorMes, $whereToRedirect);
		
			} catch (Exception $e) {
				$errorMes = 'General exceptional situation: ' . $e->getMessage();
				$whereToRedirect = ' ';
				$errorHelper = new ErrorHelper(DOC_ROOT . '/Phoenix_demo/templates');
				$errorHelper->renderErrorPageAndExit($errorMes, $whereToRedirect);
			}
		}
	}
	
	$loader = new Loader();
	$loader->main();
	ob_end_flush();