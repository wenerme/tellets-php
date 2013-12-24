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
	 * 解析文章为 Post
	 *
	 * @param $content
	 * @return Post
	 */
	public function parseContent($content)
	{
		$content = remove_byte_order_mark($content);

		$post = new Post();

		$post->setMetaDate($this->parseMetaOnly($content));
		if($post->isPublished())
			$post->setContent($this->parseContentOnly($content));

		return $post;
	}
	/**
	 * 解析文件,生成一篇文章
	 * @param $filename
	 * @return Post
	 */
	public function parseFile($filename)
	{
		$post = $this->parseContent(file_get_contents($filename));
		$post['ext'] = pathinfo($filename, PATHINFO_EXTENSION);;
		return ;
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

