<?php
namespace STANDARD\Logger;

use STANDARD\Logger\LogWriterInterface;

class LogWriterCLI implements LogWriterInterface{
	const sortieWeb = 'php://output';
	const logSeparator = PHP_EOL;
	
	protected $textNiveau = array('EMERG','ALERT','CRIT','ERR','WARN','NOTICE','INFO','DEBUG');
	
	public function write($niveau,$message){
		$ligne = date('Y-m-d H:i:s') . ' ['. $this->textNiveau[$niveau] .'] ';
		
		$ligne .= $message;
		
		fwrite(fopen(self::sortieWeb,'a'), $ligne.self::logSeparator);
	}
}

?>