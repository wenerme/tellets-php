<?php

abstract class Parser
{

	/**
	 * @param $content
	 * @return array
	 */
	public abstract function parseMetaOnly($content);

	/**
	 * @param $content
	 * @return string
	 */
	public abstract  function parseContentOnly($content);

	/**
	 * 该函数将不会自行根据文件名获取文件内容
	 *
	 * @param $content
	 * @param $filename
	 * @param null|Post $into
	 * @return Post
	 * @throws InvalidArgumentException
	 */
	public function parseContent($content, $filename, $into = null)
	{
		$content = remove_byte_order_mark($content);

		if(!$into)
			$post = new Post();
		else if($into instanceof Post)
			$post = $into;
		else
			throw new InvalidArgumentException('$info is not instanceof Post');

		$post['ext'] = pathinfo($filename, PATHINFO_EXTENSION);
		$post['hash'] = sha1($content);

		$post->setMetaDate($this->parseMetaOnly($content));
		if($post->isPublished())
			$post->setContent($this->parseContentOnly($content));

		return $post;
	}

	/**
	 * 解析文件,生成一篇文章, 使用默认的方式获取 文件内容
	 *
	 * @param $filename
	 * @param null|Post $into 将内容解析到该对象内
	 * @return Post
	 */
	public function parseFile($filename, $into = null)
	{
		return $this->parseContent(file_get_contents($filename), $filename, $into);
	}

	/**
	 * Convert the string to array.
	 * @param string $value
	 * @return string
	 */
	public function withMultiValue($value)
	{
		$item = preg_split("#[,|]#",$value);
		$item = array_map(function($v){return trim($v);}, $item);
		return $item;
	}

}

