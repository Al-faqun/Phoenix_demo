<?php
	/**
	 * Эта "админ-панель" позволяет добавлять цитаты в БД.
	 */
	use Shinoa\Model;
	use Shinoa\AdminView;
	use Shinoa\ErrorHelper;
	use Shinoa\Exception\ModelException;
	use Shinoa\Exception\ViewException;
	use Shinoa\Exception\LoaderException;
	
	Class AdminLoader 
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
			$this->model = new Model($config, $this->doc_root);
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
			
			$this->view = new AdminView($this->model, $this->doc_root, $templateDirAbs);
		}
		
		private function checkInput()
		{
			//если пользователь отправил форму с цитатой, записываем данные в сессию,
			//чтобы не терять во время перехода между страницами
			//пользовательский ввод проверяется уже при использовании сессионных переменных
			session_true_start();
			
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
			if (isset($_SESSION['textarea']) &&(isset($_SESSION['select'])) ) 
			{
				$quoteText = $_SESSION['textarea'];
				$model->insertQuote($quoteText);
			    //добавляем в базу данных запись о том, какой категории принадлежит цитата
				$select = $_SESSION['select'];
				$model->insertQuoteCategory($select);
				//эта переменная отображается на страничке после редиректа (статус)
				$_SESSION['output'] = 'Успешно добавили цитату.';    
				header('Location: ' . "/Phoenix_demo/Admin/", true, 303);
				exit;
			}

		   //каждый раз когда хотим добавить цитату, сперва проверяем пароль на правильность
			if (isset($_POST['pswd'])) 
			{
				$_SESSION['pswd'] = $_POST['pswd'];
				include $this->doc_root . '/Phoenix_demo/Private/pswd_check.php';
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
			include_once(DOC_ROOT . '/Phoenix_demo/helpers/helpers.inc.php');
			spl_autoload_register(array($this, 'autoload'));
			$root = 'D:\USR\apache\htdocs\s2.localhost';
			$this->setRoot($root);
			
			//сессия нужна для сохранения состояния "лог-ина",
			//а также позволяет передавать сообщения между страницами.
			session_true_start();
			//если введён пароль или текст цитаты для добавления
			//этот код необходим  для реализации паттерна 'post - redirect - get'
			if (isset($_SESSION['pswd']) OR isset($_SESSION['textarea'])) {
				$this->view->render('admin');
			}
	
			//если не сказано обратное, пользователь считается не-авторизированным
			$_SESSION['verified'] = 'false';
			//очищаем временную переменную, которая может хранить ненужное сообщение
			unset($_SESSION['output']);
			//загружаем страницу первый раз, все последующие запросы идут к process.php
			include DOC_ROOT . '/Phoenix_demo/Admin/tpl_main.php';
			try {
				$this->checkInput();
				
				$config_path = 
				    (file_exists($this->doc_root . '/Phoenix_demo/ini/config_test.ini')) 
					    ? $this->doc_root . '/Phoenix_demo/ini/config_test.ini'
					    : $this->doc_root . '/Phoenix_demo/ini/config_mock.ini';
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
	
	//сессия нужна для сохранения состояния "лог-ина",
	//а также позволяет передавать сообщения между страницами.
	session_true_start();

    //если введён пароль или текст цитаты для добавления
	//этот код необходим  для реализации паттерна 'post - redirect - get'
	if (isset($_SESSION['pswd']) OR isset($_SESSION['textarea'])) {
		include DOC_ROOT . '/Phoenix_demo/Admin/tpl_main.php';
		exit();
	}
	
	//если не сказано обратное, пользователь считается не-авторизированным
	$_SESSION['verified'] = 'false';
	//очищаем временную переменную, которая может хранить ненужное сообщение
	unset($_SESSION['output']);
	//загружаем страницу первый раз, все последующие запросы идут к process.php
	include DOC_ROOT . '/Phoenix_demo/Admin/tpl_main.php';
?>