<?php 
namespace Shinoa;

use Shinoa\Exception\DatabaseException;

class Connection
	{
		private $DOC_ROOT = '';
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
			return array('config');
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
		public function __construct($config) 
		{
			try {
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
		private function connect()
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
		private function close()
		{
			if (!$this->mysqli->close()) {
				$message = 'Couldn\'t close connection properly: ' . $this->mysqli->error;
				throw new DatabaseException($message) ;
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
		public function uniqueNum($left, $right, $ids)
		{
			if ((!is_int($left)) || (!is_int($right))) {
				throw new DatabaseException('One or several of parameters are not int');
			}
			
			if ($right < $left) {
				//swap'em
				$temporaryLeft = $left;
				$left = $right;
				$right = $temporaryLeft;
			}
			
			$temp_id = 0; 			
			do {
				//выбирает случайный номер цитаты
				$temp_id = mt_rand($left, $right);
			}
			//проверяет список номеров цитат на дубликаты
			while (array_search($temp_id, $ids) !== FALSE);
			
			if (!is_int($temp_id)) {
				throw new DatabaseException('Error getting unique integer');
			} else return $temp_id;
		}
		
		/**
		 * @param array $ids Method tests it's result against this array,
		 * so there are no matches.
		 * 
		 * @return integer number of quote in database
		 * @throws DatabaseException Cannot find unique quote number
		 */
		public function findUniqueNumOfQuote($ids = array())
		{
			$min = 0; $max = 0; 
			//получает диапазон номеров цитат из БД
			$this->fetchInterval($min, $max);
			
			$i = 0;
			do {
				$i++;
				if ($i > 1000) {
					throw new DatabaseException('Cannot find unique quote number');
				}
				
				$temp_id = $this->uniqueNum($min, $max, $ids);
				$result = $this->fetchQuote($temp_id);
			}
			while ($result === false);
			
			return $temp_id;
		}
		
		/**
		 * Return mysql_result, containing quote with given $id
		 * 
		 * @param int $id ID of quote
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
				$result = false;
			}
			return $result;
		}
		
		/**
		 * 
		 * @return int return last inserted id 
		 * (works only if there exists AUTOINCREMENT field
		 * in last INSERT or UPDATE query!)
		 */
		public function lastInsertedId()
		{
			$result = (int)$this->mysqli->insert_id;
			return $result;
		}
		
		/**
		 * 
		 * @param type $quoteText Text of quote to be inserted 'as is' (already escaped)
		 * @return boolean true is succeed, otherwise false
		 */
		public function insertQuote($quoteText)
		{
			$sql = "INSERT INTO `quotes` SET "
			         . "`quote`='$quoteText'";
			if (!$this->mysqli->query($sql)) {
				$result = false;
			} else $result = true;
			
			return $result;
		}
		
		/**
		 * 
		 * @param string $select text-name of quote's category 
		 * @param type $insertedQuoteId optional 
		 * @return boolean or int Int on success, otherwise false
		 */
		public function insertLastQuoteToCategory($select, $insertedQuoteId = 0)
		{
			$quoteId = ($insertedQuoteId === 0) 
			            ? $this->lastInsertedId() : $insertedQuoteId;
			$sql = "SELECT `id` FROM `categories`
			       WHERE `name`='$select' LIMIT 1";
			if ($result = $this->mysqli->query($sql)) {
				$categoryId = mysqli_fetch_row($result)[0];
				mysqli_free_result($result);
			} else { 
				return false; 
			}

			$sql = "INSERT INTO `quote_category` SET
			       `quote_id`=$quoteId,
			       `category_id`=$categoryId";
			if (!$this->mysqli->query($sql))
			{
				return false;
			}
			else return $quoteId;
		}
		
		/**
		 * 
		 * @param int $quoteId ID of quote to be deleted
		 * @throws DatabaseException Cannot delete selected quote
		 */
		public function deleteQuoteById($quoteId)
		{
			$sql = "DELETE FROM `quotes` WHERE `id`=$quoteId";
			
			if (!$result = $this->mysqli->query($sql)) {
				$message = 'Cannot delete selected quote';
				throw new DatabaseException($message);
			}
		}
		
		/**
		 * 
		 * @param int $quoteId ID of quote, which category record must be deleted
		 * @throws DatabaseException
		 */
		public function deleteQuoteCategoryById($quoteId)
		{
			$sql = "DELETE FROM `quote_category` WHERE `quote_id`=$quoteId";
			
			if (!$result = $this->mysqli->query($sql)) {
				$message = 'Cannot delete selected quote-category record';
				throw new DatabaseException($message);
			}
		}
	}