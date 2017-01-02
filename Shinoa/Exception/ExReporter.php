<?php
namespace Shinoa\Exception;

/**
 * Description of ExceptionPeporter
 *
 * @author Shinoa
 */
class ExReporter
{
	public function __construct() 
	{
	}
	
	public static function doGetText(\Exception $e, $newLineChar)
	{
		$outputString = '';
		$format = "At '%s', on line: %d %s (exception class %s: code %d)";
		do {
			$fileName = strrchr($e->getFile(), DIRECTORY_SEPARATOR);
			$outputString .= sprintf($format, $fileName, $e->getLine(), $e->getMessage(), 
					                       get_class($e), $e->getCode()) . $newLineChar;
		}
		while ($e = $e->getPrevious());	
		return $outputString;
	}
	public static function string(\Exception $e)
	{	
		return self::doGetText($e, PHP_EOL);
	}
	
	public static function HTML(\Exception $e)
	{
		return self::doGetText($e, "</br>");
	}
	
}


