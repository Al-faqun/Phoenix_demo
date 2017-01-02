<?php
namespace Shinoa;

	use Shinoa\Exception\ModelException;
	use Shinoa\Exception\DatabaseException;
	
	/**
	 * Model
	 */
	class QuoteManager
	{
		/**
		 * Path to changelog
		 */
		private $changelog = '';
		
		/**
		 * @var class_Connection 
		 */
		private $db = null;
		
		/**
		 *
		 * @var int 
		 */
		private $numOfQuotes = 6;
		/**
		 *
		 * @var bool 
		 */
		private $color_on = true;
		
		/**
		 * Получает доступ к БД
		 * 
		 * @param array $config Parsed ini file for db connection
		 */
		public function __construct($config) 
		{
			try {
				$this->db = new Connection($config);
			} catch (DatabaseException $e) {
				$exMes = 'Cannot construct connection: ' . $e->getMessage();
				throw new ModelException($exMes);
			}
		}

		/**
		 * 
		 * @param integer $numOfQuotes 
		 */
		public function setNumOfQuotes($numOfQuotes)
		{
			if (is_int($numOfQuotes)) {
				$this->numOfQuotes = $numOfQuotes;
			} else {
				$exMes = 'Cannot set number of quotes other than integer';
				throw new ModelException($exMes);
			}
		}
		
		/**
		 * 
		 * @return integer Number of quotes model currently is set to retrieve
		 * OR FALSE on failure.
		 */
		public function getNumOfQuotes() 
		{
			if (isset($this->numOfQuotes)) {
				$result = $this->numOfQuotes;
			} else {
				$message = 'Number of quotes to retrieve is not set';
				throw new ModelException($message);
			}
			return $result;
		}
		
		/**
		 * 
		 * @param bool $color_on If color of quotes is set on
		 */
		public function setColor($color_on) 
		{
			if (is_bool($color_on)) {
				$this->color_on = $color_on;
			} else throw new ModelException('Color code is of incorrect type');
		}
		
		/**
		 * @return bool $color_on If color of quotes is set on
		 * 
		 * @throws ModelException Color code is not set 
		 */
		public function getColor() 
		{
			if (isset($this->color_on)) {
				$result = $this->color_on;
			} else throw new ModelException('Color code is not set');
			return $result;
		}
		
		/**
		 * 
		 * @param string $changeLogPath absolute path
		 * @throws ModelException Not valid path
		 */
		public function setChangeLogPath($changeLogPath)
		{
			if (file_exists($changeLogPath)) {
				$this->changelog = $changeLogPath;
			} else {
				throw new ModelException('Changelog path is not valid');
			}
			
		}
		/**
		 * 
		 * @return array Array of strings OR FALSE
		 */
		public function getChangeLog() 
		{
			$result = '';
			if (file_exists($this->changelog) && is_readable($this->changelog)) {
				$changes = file($this->changelog, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
				$result = $changes;
			} else $result = false;
			return $result;
		}
		
		/**
		 * 
		 * @return array Array of strings, each string a citation.
		 * If quote text cannot be returned, 
		 * default string 'Cannot fetch quote' is returned.
		 * 
		 * @throws ModelException Could not get quotes from DB
		 */
		public function getQuotes() 
		{
			try {
				$citations = array();
				
				//инициализирует массив id цитат  и их число 
				$ids[0] = 0;
				$count = $this->getNumOfQuotes();	 
				//для каждой из шести цитат
				for ($i = 0; $i < $count; $i++)
				{
					$id = $this->db->findUniqueIDOfQuote($ids);
					if ($id === false) {
						$citations[] = 'Cannot fetch quote';
					}
					$citations[] = $this->db->fetchQuote($id);
				}
				return $citations;
			} catch	(DatabaseException $e) {
				throw new ModelException('Could not get quotes from DB: ' . $e->getMessage());
			}
		}
		
		/**
		 * Escapes and inserts quote text
		 * 
		 * @param string $quote_text
		 * @throws ModelException Cannot add quote to db
		 */
		public function insertQuote($quote_text) 
		{
			$quoteText = Connection::esc($quote_text);
			if (!$this->db->insertQuote($quote_text)) {
				$message = 'Couldn\'t add quote to db';
				throw new ModelException($message);
			}
		}
		
		/**
		 * 
		 * @param type $select
		 * @throws ModelException
		 */
		public function insertQuoteCategory($select)
		{
			$category = Connection::esc($select);
			if (!$this->db->insertLastQuoteToCategory($category)) {
				$message = 'Couldn\'t add quote category record to db';
				throw new ModelException($message);
			}
		}
	}
	

	