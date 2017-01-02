<?php
namespace Shinoa\Tests;
use \Shinoa\Connection;

/**
 * Description of ConnectionTest
 *
 * @author Shinoa
 */
class ConnectionTest extends \PHPUnit_Framework_TestCase 
{
	protected $db = null;
	protected $insertedQuoteIds = array();
	protected $insertedQuoteCategory = array();
	
	public function setUp() 
	{
		$config_path = DOC_ROOT . '/Phoenix_demo/ini/config_mock.ini';
		$config = parse_ini_file($config_path);
		$this->db = new Connection($config);
	}
	
	public function tearDown() 
	{
		if (!empty($this->insertedQuoteIds)) foreach ($this->insertedQuoteIds as $quoteId) {
			$this->db->deleteQuoteById($quoteId);
		}
		
		if (!empty($this->insertedQuoteCategory))foreach ($this->insertedQuoteCategory as $quoteId) {
			$this->db->deleteQuoteCategoryById($quoteId);
		}
	}
	
	public function testOnConstructNotNull() 
	{
		$this->assertNotNull($this->db);
		
	}
	
	public function testOnConstructIsConnectionClass()
	{
		$this->assertInstanceOf(Connection::class, $this->db);
		
	}
	
	public function testOnConstructWrongConfigFail()
	{
		$this->expectException(\Shinoa\Exception\DatabaseException::class);
		$config = false;
		$this->db = new Connection($config);
	}
	
	public function testSetCharsetFail()
	{
		$this->expectException(\Shinoa\Exception\DatabaseException::class);
		$this->db->setCharset('mock_charset');
	}
	
	public function testSelectDbFail()
	{
		$this->expectException(\Shinoa\Exception\DatabaseException::class);
		$this->db->selectDb('mock_db');
	}
	
	public function testQueryFail()
	{
		$sql = 'SELICT * FROM TABLE';
		$errMes = 'Wrong SELICT';
		$this->expectException(\Shinoa\Exception\DatabaseException::class);
		$this->db->query($sql, $errMes);
	}
	
	public function testQueryRowFail()
	{
		$sql = 'SELICT * FROM TABLE';
		$errMes = 'Wrong SELICT';
		$this->expectException(\Shinoa\Exception\DatabaseException::class);
		$this->db->queryRow($sql, $errMes);
	}

	public function testFetchInterval()
	{
		$min = 0;
		$max = 0;
		$this->db->fetchInterval($min, $max);
		$this->assertInternalType('integer', $min);
		$this->assertInternalType('integer', $max);
	}
	
	public function testFindUniqueNumOfQuote()
	{
		$int = $this->db->findUniqueIDOfQuote();
		$this->assertInternalType('integer', $int);
	}
	
	public function testFetchQuote()
	{
		$id = $this->db->findUniqueIDOfQuote();
		$result = $this->db->fetchQuote($id);
		$this->assertNotFalse($result);
	}
	
	public function testInsertQuote()
	{
		$quoteText = 'test text';
		$result = $this->db->insertQuote($quoteText);
		$this->assertTrue($result);
		
		$insertedId = $this->db->lastInsertedId();
		array_push($this->insertedQuoteIds, $insertedId);
		
		return $insertedId;
	}
	
	/**
	 * 
	 * @depends testInsertQuote
	 */
	public function testInsertLastQuoteToCategory($insertedId)
	{	
		$select = 'Other';
		$result = $this->db->insertLastQuoteToCategory($select, $insertedId);
		$this->assertNotFalse($result);
		
		array_push($this->insertedQuoteCategory, $insertedId);
	}
}
