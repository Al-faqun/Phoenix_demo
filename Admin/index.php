<?php
	/**
	 * Эта "админ-панель" позволяет добавлять цитаты в БД.
	 */

	//буферизация
	ob_start();
	
	use \Shinoa\QuoteManager;
	use \Shinoa\AdminView;
	use \Shinoa\ErrorHelper;
	use \Shinoa\Helpers;
	use \Shinoa\Exception\ModelException;
	use \Shinoa\Exception\ViewException;
	use \Shinoa\Exception\LoaderException;
	use \Shinoa\Exception\ExReporter;
	
	Class AdminLoader 
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
			$this->model = new QuoteManager($config, $this->docRoot);
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
			
			$this->view = new AdminView($this->model, $this->docRoot, $templateDirAbs);
		}
		
		private function checkInput()
		{
			//если пользователь отправил форму с цитатой, записываем данные в сессию,
			//чтобы не терять во время перехода между страницами
			//пользовательский ввод проверяется уже при использовании сессионных переменных
		
			if (isset($_POST['textarea'])) 
			{
				$_SESSION['textarea'] = $_POST['textarea'];
				if (isset($_POST['select'])) 
				{
					$_SESSION['select'] = $_POST['select'];
				}
			   //редирект на ту же страницу, заодно очищает массив $_POST
			   //здесь и далее при помощи редиректов реализуем шаблон post - redirect - get, благодаря которому F5 не отсылает данные заново
				header('Location: ' . "/Phoenix_demo/Admin/", true, 303);
				exit();
			}

			//после редиректа: обрабатываем данные из формы, добавляем цитату в базу данных
			if (isset($_SESSION['textarea']) && (isset($_SESSION['select'])) ) 
			{
				$quoteText = Connection::esc($_SESSION['textarea']);
				$this->model->insertQuote($quoteText);
			    //добавляем в базу данных запись о том, какой категории принадлежит цитата
				$select =  Connection::esc($_SESSION['select']);
				$this->model->insertQuoteCategory($select);
				//эта переменная отображается на страничке после редиректа (статус)
				$_SESSION['output'] = 'Успешно добавили цитату.';    
				header('Location: ' . "/Phoenix_demo/Admin/", true, 303);
				exit;
			}

		   //каждый раз когда хотим добавить цитату, сперва проверяем пароль на правильность
			if (isset($_POST['pswd'])) 
			{
				$_SESSION['pswd'] = $_POST['pswd'];
				include $this->docRoot . '/Phoenix_demo/Private/pswd_check.php';
				//возвращаемся к главному скрипту админки
				header('Location: ' . "/Phoenix_demo/Admin/", true, 303);    
				exit;
			}

			//если пользователь хочет вернуться на главную страницу
			if (isset($_POST['back'])) 
			{
				header('Location: ' . "/Phoenix_demo/", true);
				exit;
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
				$this->loadView('/Phoenix_demo/Admin');
				
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
			
			/*//если введён пароль или текст цитаты для добавления
			//этот код необходим  для реализации паттерна 'post - redirect - get'
			if (isset($_SESSION['pswd']) || isset($_SESSION['textarea'])) {
				$this->view->render();
			} */
	
			
		}
	}

$loader = new AdminLoader();
$loader->main();
ob_end_flush();