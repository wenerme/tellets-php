<?php

final class ParserFactory
{
	private static $parser = array();
	private static $instances = array();

	/**
	 * @param string $filename
	 * @return null|Parser
	 */
	public static function getParser($filename)
	{

		$ext = self::getMatchExt($filename);
		if($ext == null)
			return null;

		$instance = &self::$instances[$ext];
		if($instance == null)
		{
			$class = self::$parser[$ext];
			$instance = new $class;
		}

		return $instance;
	}

	private static function getMatchExt($filename)
	{
		foreach(self::$parser as $ext => $class)
			if(preg_match($ext,$filename))
			{
				return $ext;
				break;
			}
		return null;
	}

	/**
	 * @param $filename
	 * @return bool
	 */
	public static function CanParse($filename)
	{
		return self::getMatchExt($filename) != null;
	}

	/**
	 * 注册解析器
	 * @param string $ext 匹配文件名的正则表达式
	 * @param String $classname
	 * @throws InvalidArgumentException
	 */
	public static function RegisterParser($ext,$classname)
	{
		if(false == class_exists($classname))
			throw new InvalidArgumentException("Class '$classname' not exists");
		elseif(is_subclass_of($classname,'PostParser'))
			throw new InvalidArgumentException("Class '$classname' is not the subclass of PostParser");

		self::$parser[$ext] = $classname;
	}

	/**
	 * 移除已经添加的 解析器
	 * @param $ext
	 */
	public static function RemoveParser($ext)
	{
		unset(self::$parser[$ext]);
		unset(self::$instances[$ext]);
	}
	/**
	 * 尝试解析文件到Post,如果不能解析,返回null
	 *
	 * @param $filename
	 * @return null|Post
	 */
	public static function TryParseFile($filename)
	{
		$parser = self::getParser($filename);
		if($parser != null)
			return $parser->parseFile($filename);
		return null;
	}
} 