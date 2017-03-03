<?php


class Autoloader{
	private static $namespace ;

	public static function register(array $config){
		self::$namespace = $config['ns']; 
		spl_autoload_register(array(__CLASS__, 'autoload'));
	}
	
	
	public static function autoload($class){
		$namespace = explode("\\", $class);
		$path = "";
		foreach(self::$namespace as $key => $value){
			if($key == $namespace[0]){
				$path = $value;
			}
		}
		$class = str_replace("\\", DIRECTORY_SEPARATOR, $class);
	
		
		require_once $path.$class.".php";
	}
}