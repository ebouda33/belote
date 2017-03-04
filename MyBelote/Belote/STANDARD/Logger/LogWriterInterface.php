<?php
namespace STANDARD\Logger;

interface  LogWriterInterface {

	public function write($niveau,$message);
}

?>