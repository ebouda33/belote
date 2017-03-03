<?php

namespace WebSocket\Application;

/**
 * WebSocket Server Application
 * 
 * @author Nico Kaiser <nico@kaiser.me>
 */
abstract class Application
{
    protected static $instances = array();
    
    /**
     * Singleton 
     */
    protected function __construct() { }

    final private function __clone() { }
    
    final public static function getInstance()
    {
        $calledClassName = get_called_class();
        if (!isset(self::$instances[$calledClassName])) {
            self::$instances[$calledClassName] = new $calledClassName();
        }

        return self::$instances[$calledClassName];
    }

    abstract public function onConnect($connection);

	abstract public function onDisconnect($connection);

	abstract public function onData($data, $client);

	// Common methods:
	
	protected function _decodeData($data)
	{
		$decodedData = json_decode($data, true);
		if($decodedData === null)
		{
			return false;
		}
		
		if(isset($decodedData['action'], $decodedData['data']) === false)
		{
			return false;
		}
		
		return $decodedData;
	}
	
	protected function _encodeData($action, $data)
	{
		if(empty($action))
		{
			return false;
		}
		
		$payload = array(
			'action' => $action,
			'data' => $data
		);
		
		return json_encode($payload);
	}
	
	protected function log($message, $type = 'info')
    {
    	$classe = get_called_class();
    	$application = substr($classe,(strripos($classe,"\\"))+1,strlen($classe));
        echo date('Y-m-d H:i:s') . ' [' . ($type ? $type : 'error') . '] ['.$application.']' . $message . PHP_EOL;
    }
	
		
}