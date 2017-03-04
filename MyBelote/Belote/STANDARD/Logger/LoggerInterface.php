<?php
namespace STANDARD\Logger;
use Traversable;


interface LoggerInterface {

	
	/**
	 * @param string $message
	 * @param array|Traversable $extra
	 * @return LoggerInterface
	 */
	public function emerg($message, $extra = array());
	
	/**
	 * @param string $message
	 * @param array|Traversable $extra
	 * @return LoggerInterface
	 */
	public function alert($message, $extra = array());
	
	/**
	 * @param string $message
	 * @param array|Traversable $extra
	 * @return LoggerInterface
	 */
	public function crit($message, $extra = array());
	
	/**
	 * @param string $message
	 * @param array|Traversable $extra
	 * @return LoggerInterface
	 */
	public function err($message, $extra = array());
	
	/**
	 * @param string $message
	 * @param array|Traversable $extra
	 * @return LoggerInterface
	 */
	public function warn($message, $extra = array());
	
	/**
	 * @param string $message
	 * @param array|Traversable $extra
	 * @return LoggerInterface
	 */
	public function notice($message, $extra = array());
	
	/**
	 * @param string $message
	 * @param array|Traversable $extra
	 * @return LoggerInterface
	 */
	public function info($message, $extra = array());
	
	/**
	 * @param string $message
	 * @param array|Traversable $extra
	 * @return LoggerInterface
	 */
	public function debug($message, $extra = array());
}

?>