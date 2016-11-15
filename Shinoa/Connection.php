<?php 
namespace Shinoa;

use Shinoa\Exception\DatabaseException;

class Connection
	{
		private $DOC_ROOT = 'D:/USR/apache/htdocs/s2.localhost';
		private $mysqli = null;
		private $config = null;
		
		/**
		 * Method is called before object is serialized
		 * 
		 * @return array
		 */
		public function __sleep() 
		{
			$this->close();
			return array('DOC_ROOT', 'config');
		} 
		
		/**
		 * Method is called after object is unserialized
		 */
		public function __wakeup() 
		{
			$this->connect();
			$this->setCharset('utf8');
			$this->selectDb($this->config['dbname']);
		} 
		
		/**
		 * 
		 * @param array $config Parsed ini file
		 * @param string $doc_root Path to document root of webserver (absolute)
		 * @throws DatabaseException Error creating Connection
		 */
		public function __construct($config, $doc_root) 
		{
			try {
				$this->setRoot($doc_root);
				$this->setConfig($config);
				$this->connect();
				$this->setCharset('utf8');
				$this->selectDb($this->config['dbname']);
			} catch (DatabaseException $e) {
				throw new DatabaseException($e->getMessage());
			}
		}
		
		/**
		 * 
		 * @throws DatabaseException Connection destruction
		 */
		public function __destruct()
		{
			try {
				$this->close();
			} catch (DatabaseException $e) {
				throw new DatabaseException('Connection destruction:' . $e->getMessage());
			}
		}
		
		/**
		 * Creates brand new mysqli object
		 * 
		 * After using it, you must select database, because it's not selected
		 * 
		 * @throws DatabaseException Failed to connect to MySQL
		 */
		public function connect()
		{
			mysqli_report(MYSQLI_REPORT_STRICT);
			try {
				$this->mysqli = new \mysqli('localhost', $this->config['username'], 
			                                             $this->config['password']);
			} catch (\mysqli_sql_exception $e) { 
					$message = 'Failed to connect to MySQL.';
					throw new DatabaseException($message);		 
			}
			

		}
		
		/**
		 * Closes current mysqli connection to db
		 * 
		 * @throws DatabaseException Could not close connection properly
		 */
		public function close()
		{
			if (!$this->mysqli->close()) {
				$message = 'Couldn\'t close connection properly: ' . $this->mysqli->error;
				throw new DatabaseException($message) ;
			}
		}
		
		/**
		 * 
		 * @param String $doc_root
		 * @throws DatabaseException Not valid root
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
		 * Sets $config of object
		 * 
		 * @param array $config Parsed ini file
		 * 
		 * @throws DatabaseException No config
		 * @throws DatabaseException Incorrectly filled config
		 */
		public function setConfig($config) 
		{
			if (empty($config)) {
				$message = 'No config! Please, specify config before connecting to db';
				throw new DatabaseException($message);
			}
			if  ((!key_exists('username', $config)) 
					||
			     (!key_exists('password', $config))
					||
			     (!key_exists('dbname', $config))
				) {
					$message = 'Incorrectly filled config!';
					throw new DatabaseException($message);
				}	
			$this->config = $config;
		}
		
		/**
		 * Sets charset of db connection
		 * 
		 * @param string $charset Legit mysql charset name
		 * 
		 * @throws Exception Error setting DB charset encoding
		 */
		public function setCharset($charset) 	
		{
			if (!$this->mysqli->set_charset($charset)) {
				$message = 'Unable to set Database connection encoding';
				throw new DatabaseException($message);
			}
		}
		
		/**
		 * Selects database in current connection
		 * 
		 * @param string $dbName Name of existing database
		 * 
		 * @throws DatabaseException Unable to locate selected database
		 */
		public function selectDb($dbName) 
		{
			if (!$this->mysqli->select_db($dbName)) 
			{
				$message = 'Unable to locate selected database.';
				throw new DatabaseException($message);
			}
		}
		
		/**
		 * Performs query  in mysql 
		 * 
		 * @param string $sql Sequence
		 * @param string $errMes Message, which is displayed upon failure
		 * 
		 * @throws DatabaseException If there is no result from query
		 * 
		 * @return array Result of sequence in form of numerical array 
		 */
		public function query($sql, $errMes) 
		{
			$result = $this->mysqli->query($sql);
			
			if (!$result)
			{
				$message = $errMes;
				throw new DatabaseException($message);
			}
			
			return $result;
		}
		
		/**
		 * Performs query in mysql, returns row
		 * 
		 * @param string $sql Sequence
		 * @param string $errMes Message, which is displayed upon failure
		 * 
		 * @throws DatabaseException If there is no result from query
		 * 
		 * @return mixed Numerical array containing first row from requested seqence or NULL 
		 */
		public function queryRow($sql, $errMes) 
		{
			$result = $this->mysqli->query($sql);
			
			if (!$result)
			{
				$message = $errMes;
				throw new DatabaseException($message);
			}
			else return mysqli_fetch_row($result);
		}
		
		/**
		 * Performs search of lowest and highest ids in `quotes` table
		 * Saves them in referenced variables
		 * 
		 * @param &int $min Reference to variable, where to save left border of interval
		 * @param &int $max Reference to variable, where to save right border of interval
		 */
		public function fetchInterval(&$min, &$max) 
		{
			$sql = 'SELECT MIN(id), MAX(id) FROM `quotes`';
			$errMes = 'Error fetching quotes of Phoenix from db!';
			$row = $this->queryRow($sql, $errMes);
			if ($row !==NULL) {
				$min = (int)$row[0];
				$max = (int)$row[1];
			}
			else 
				throw new DatabaseException('Cannot get correct interval of quotes');
		}
		
		/**
		 * Returns a random int in interval, 
		 * which is unique against given array $ids
		 * 
		 * @param int $min Left border of interval
		 * @param int $max Right border of interval
		 * @param array $ids Already stored numbers
		 * 
		 * @throws DatabaseException If One or several of parametres are not int
		 * @throws DatabaseException If $max < $min
		 * @throws DatabaseException If Cannot return int
		 * 
		 * @return int
		 */
		public function uniqueNum($min, $max, $ids)
		{
			if ((!is_int($min)) || (!is_int($max))) {
				throw new DatabaseException('One or several of parameters are not int');
			}
			
			if ($max < $min) {
				throw new DatabaseException('Max number must be bigger than min number');
			}
			
			$temp_id = 0; 			
			do {
				//выбирает случайный номер цитаты
				$temp_id = mt_rand($min, $max);
			}
			//проверяет список номеров цитат на дубликаты
			while (array_search($temp_id, $ids) !== FALSE);
			
			if (!is_int($temp_id)) {
				throw new DatabaseException('Error getting unique integer');
			} else return $temp_id;
		}
			
		/**
		 * Return mysql_result, containing quote with given $id
		 * 
		 * @param int $id ID of  quote
		 * 
		 * @throws DatabaseException No result 
		 * 
		 * @return mixed Mysql_result or FALSE upon failure
		 */
		public function fetchQuote($id)
		{
			$sql = "SELECT `quote` FROM `quotes` WHERE `id` = $id";
			$errMes = 'Error fetching quotes of Phoenix from db! ';
			$result = $this->query($sql, $errMes);
			
			if (mysqli_num_rows($result) === 0)
			{
				throw new DatabaseException('No result after fetching quote');
			}
			return $result;
		}
	}