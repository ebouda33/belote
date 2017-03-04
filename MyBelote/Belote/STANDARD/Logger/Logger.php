<?php
namespace STANDARD\Logger;

use STANDARD\Logger\LogWriterWeb;

use STANDARD\Exeception\LoggerException;

use STANDARD\Logger\LogWriterInterface;

use STANDARD\Logger\LogWriter;

use STANDARD\Logger\LoggerInterface;

class Logger implements LoggerInterface{
	
	const EMERG  = 0;
	const ALERT  = 1;
	const CRIT   = 2;
	const ERR    = 3;
	const WARN   = 4;
	const NOTICE = 5;
	const INFO   = 6;
	const DEBUG  = 7;
	
	public static $errorPriorityMap = array(
			E_NOTICE            => self::NOTICE,
			E_USER_NOTICE       => self::NOTICE,
			E_WARNING           => self::WARN,
			E_CORE_WARNING      => self::WARN,
			E_USER_WARNING      => self::WARN,
			E_ERROR             => self::ERR,
			E_USER_ERROR        => self::ERR,
			E_CORE_ERROR        => self::ERR,
			E_RECOVERABLE_ERROR => self::ERR,
			E_STRICT            => self::DEBUG,
			E_DEPRECATED        => self::DEBUG,
			E_USER_DEPRECATED   => self::DEBUG,
	);
	
	
	private $writers = array(); 
	
	
	public function __construct(){
		if(php_sapi_name() === 'cli'){
			$this->addWriter(new LogWriterCLI());
		}else{
			$this->addWriter(new LogWriterWeb());
		}	
	}
	
	public function addWriter($writer){
		if($writer instanceof  LogWriterInterface){
			array_push($this->writers,$writer);
		}else{
			throw new LoggerException("Ajout de writer Impossible ");
		}
	}
	
	private function doWrite($niveau,$message,$extra){
		foreach ($this->writers as $writer){
			if($writer instanceof LogWriterInterface){
				$writer->write($niveau,$message);
			}
		}
		
	}
	
	/* (non-PHPdoc)
	 * @see STANDARD\Log.LoggerInterface::alert()
	 */public function alert($message, $extra = array()) {
		// TODO Auto-generated method stub
		$this->doWrite(self::ALERT, $message, $extra);
		}

	/* (non-PHPdoc)
	 * @see STANDARD\Log.LoggerInterface::crit()
	 */public function crit($message, $extra = array()) {
		// TODO Auto-generated method stub
		$this->doWrite(self::CRIT, $message, $extra);
		}

	/* (non-PHPdoc)
	 * @see STANDARD\Log.LoggerInterface::debug()
	 */public function debug($message, $extra = array()) {
		// TODO Auto-generated method stub
		$this->doWrite(self::DEBUG, $message, $extra);
		}

	/* (non-PHPdoc)
	 * @see STANDARD\Log.LoggerInterface::emerg()
	 */public function emerg($message, $extra = array()) {
		// TODO Auto-generated method stub
		$this->doWrite(self::EMERG, $message, $extra);
		}

	/* (non-PHPdoc)
	 * @see STANDARD\Log.LoggerInterface::err()
	 */public function err($message, $extra = array()) {
		// TODO Auto-generated method stub
		$this->doWrite(self::ERR, $message, $extra);
		}

	/* (non-PHPdoc)
	 * @see STANDARD\Log.LoggerInterface::info()
	 */public function info($message, $extra = array()) {
		// TODO Auto-generated method stub
		$this->doWrite(self::INFO, $message, $extra);
		}

	/* (non-PHPdoc)
	 * @see STANDARD\Log.LoggerInterface::notice()
	 */public function notice($message, $extra = array()) {
		// TODO Auto-generated method stub
		$this->doWrite(self::NOTICE, $message, $extra);
		}

	/* (non-PHPdoc)
	 * @see STANDARD\Log.LoggerInterface::warn()
	 */public function warn($message, $extra = array()) {
		// TODO Auto-generated method stub
		$this->doWrite(self::WARN, $message, $extra);
		}

	
	
}

?>