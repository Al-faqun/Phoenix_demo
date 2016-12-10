<?php
namespace Shinoa;

	use Shinoa\Exception\ModelException;
	use Shinoa\Exception\DatabaseException;
	
	/**
	 * Model
	 */
	class Model
	{
		/**
		 * Path to changelog
		 */
		const CHANGE_LOG = 'D:/USR/apache/htdocs/s2.localhost' . '/Phoenix_demo/changelog.txt';
		
		/**
		 * @var class_Connection 
		 */
		private $db;
		
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
		 */
		public function __construct($config) 
		{
			try {
				$this->db = new Connection($config);
			} catch (DatabaseException $e) {
				throw new ModelException('Cannot construct connection: ' . $e->getMessage());
			}
		}
		
		/**
		 * Цвет цитат на странице
		 * 
		 * @param bool $color_on
		 */
		
		/**
		 * 
		 * @param integer $numOfQuotes 
		 */
		public function setNumOfQuotes($numOfQuotes)
		{
			if (is_int($numOfQuotes)) {
				$this->numOfQuotes = $numOfQuotes;
			} else throw new ModelException('Cannot set number of quotes other than integer');
		}
		
		/**
		 * 
		 * @return integer Number of quotes model currently is set to retrieve
		 */
		public function getNumOfQuotes() 
		{
			return $this->numOfQuotes;
		}
		
		/**
		 * 
		 * @param bool $color_on If color of quotes is set on
		 */
		public function setColor($color_on) 
		{
			if (is_bool($color_on)) {
				$this->color_on = $color_on;
			} else throw new ModelException('Cannot set color code other than integer');
		}
		
		/**
		 * @return bool $color_on If color of quotes is set on
		 */
		public function getColor() 
		{
			return $this->color_on;
		}
		
		/**
		 * 
		 * @return array Array of strings, each string a line
		 */
		public function getChanges() 
		{
			$result = '';
			if (file_exists(self::CHANGE_LOG) && is_readable(self::CHANGE_LOG)) {
				$changes = file(self::CHANGE_LOG, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
				$result = $changes;
			} else $result = 'Changelog not found.';
			return $result;
		}
		
		/**
		 * 
		 * @return array Array of strings, each string a citation
		 */
		public function getQuotes() 
		{
			try {
				$min = 0; $max = 0;
				//получает диапазон номеров цитат из БД
				$this->db->fetchInterval($min, $max);
			
				//инициализирует массив цитат  и их число 
				$ids[0] = 0;
				$count = $this->getNumOfQuotes();	 
				//для каждой из шести цитат
				for ($i = 0; $i < $count; $i++)
				{
					$id = $this->db->findUniqueNumOfQuote($ids);
					$result = $this->db->fetchQuote($id);
					$citations[] = mysqli_fetch_array($result)[0];
				}
				return $citations;
			} catch	(DatabaseException $e) {
				throw ModelException('Could not get quotes from DB: ' . $e->getMessage());
			}
		}
		
		public function insertQuote($quote_text) 
		{
			$quoteText = $this->mysqli->real_escape_string($quoteText);
			if (!$this->db->insertQuote($quote_text)) {
				$message = 'Couldn\'t add quote to db';
				throw new ModelException($message);
			}
		}
		
		public function insertQuoteCategory($select)
		{
			$select = $mysqli->real_escape_string($select);
			$this->db->insertQuoteCategory($select);
		}
	}
	

	