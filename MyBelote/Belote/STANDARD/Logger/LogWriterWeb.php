<?php
namespace STANDARD\Logger;

use STANDARD\Logger\LogWriterInterface;

class LogWriterWeb implements LogWriterInterface{
	const sortieWeb = 'php://output';
	const logSeparator = "<br>";
	
	
	public function write($niveau,$message){
		$ligne = "";
		if(php_sapi_name() !== 'cli'){
		if($niveau == Logger::ERR){
				$ligne = "<span style='color:red;'>";
			}elseif($niveau == Logger::DEBUG){
				$ligne = "<span style='color:green;'>";
			}elseif($niveau == Logger::WARN){
				$ligne = "<span style='color:orange;'>";
			}elseif($niveau == Logger::NOTICE){
				$ligne = "<span style='color:blue;'>";
			}else{
			
				$ligne = "<span style='color:#000;'>";
			}
		}
		
		$ligne .= $message;
		if(php_sapi_name() !== 'cli'){
			$ligne .= "</span>";
		}
		fwrite(fopen(self::sortieWeb,'a'), $ligne.self::logSeparator);
	}
}

?>