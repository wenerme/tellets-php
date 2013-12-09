<?php

class PostParser
{
	private static $parser = array();

	public static function getParser($filename)
	{
		$instance = null;
		foreach(self::$parser as $ext => $class)
			if(preg_match($ext,$filename))
			{
				$instance = new $class();
				break;
			}

		return $instance;
	}

	public static function RegisterParser($ext, $class)
	{
		if(false == class_exists($class))
			throw new InvalidArgumentException("Class '$class' not exists");
		elseif(is_subclass_of($class,'PostParser'))
			throw new InvalidArgumentException("Class '$class' is not the subclass of PostParser");

		self::$parser[$ext] = $class;
	}
}

