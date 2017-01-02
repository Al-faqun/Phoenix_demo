<?php
namespace Shinoa\Tests;
	use \Shinoa\QuoteManager;

	class QuoteManagerTest extends \PHPUnit_Framework_TestCase 
	{
		protected $quoteManager = null;
		
		public function setUp() 
		{
			$config_path = DOC_ROOT . '/Phoenix_demo/ini/config_mock.ini';
			$config = parse_ini_file($config_path);
			$this->quoteManager = new QuoteManager($config);
		}
	
		public function tearDown() 
		{
		}
		
		public function testConstructFailWithException()
		{
			$config = array();
			$this->expectException(\Shinoa\Exception\ModelException::class);
			$this->quoteManager = new QuoteManager($config);
		}
		
		public function testSetNumOfQuotes()
		{
			$numOfQuotes = 5;
			$this->quoteManager->setNumOfQuotes($numOfQuotes);
		}
		
		public function testSetNumOfQuotesNotIntFail()
		{
			$numOfQuotes = 'not int';
			$this->expectException(\Shinoa\Exception\ModelException::class);
			$this->quoteManager->setNumOfQuotes($numOfQuotes);
		}
		
		public function testSetAndGetNumOfQuotes()
		{
			$numOfQuotes = 5;
			$this->quoteManager->setNumOfQuotes($numOfQuotes);
			
			$this->assertInternalType('integer', $this->quoteManager->getNumOfQuotes());
		}
		
		public function testSetColorNotBooleanFail()
		{
			$color_on = 'not boolean';
			$this->expectException(\Shinoa\Exception\ModelException::class);
			$this->quoteManager->setColor($color_on);
		}
		
		public function testSetAndGetColor()
		{
			$color_on = false;
			$this->quoteManager->setColor($color_on);
			
			$this->assertFalse($this->quoteManager->getColor());
		}
		
		public function testSetChangeLogPathAndGetChangeLog()
		{
			$changeLogPath = DOC_ROOT . '/Phoenix_demo/changelog.txt';
			$this->quoteManager->setChangeLogPath($changeLogPath);
			
			$changes = $this->quoteManager->getChangeLog();
			$this->assertNotFalse($changes);
		}
	
	}
