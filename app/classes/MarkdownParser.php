<?php

use \Michelf\MarkdownExtra;
include_once LIB_DIR.'markdown.php';

class MarkdownParser extends Parser
{

	/**
	 * @param $content
	 * @return array
	 */
	public function parseMetaOnly($content)
	{
		// 解析元字段
		$meta = array();

		// match all meta out
		preg_match('#(^\s*<!--([^:]+):(.*)-->\s*$[\n\r]+)+#m',$content,$all);

		if(false == isset($all[0]))
			goto COMPLETE_PARSE_META;

		// match key/value pair
		preg_match_all('#^\s*<!--([^:]+):(.*)-->\s*$[\n\r]+#m',$all[0],$matches);

		foreach($matches[1] as $k => $v)
		{
			$key = $v;
			$val = $matches[2][$k];
			// preprocess
			$key = trim($key);
			$val = trim($val);

			$key = preg_replace('#\s+#',' ', $key);

			$meta[$key] = $val;
		}

		// deal the multi value field
		isset($meta[Post::CATEGORY_META]) && $meta[Post::CATEGORY_META] = $this->withMultiValue($meta[Post::CATEGORY_META]);
		isset($meta[Post::TAG_META]) && $meta[Post::TAG_META] = $this->withMultiValue($meta[Post::TAG_META]);

		// parse date
		isset($meta[Post::DATE_META]) && $meta[Post::DATE_META] = strtotime($meta[Post::DATE_META]);

		// state
		isset($meta[Post::STATE_META]) || $meta[Post::STATE_META] = 'draft';

		// hash
		$meta[Post::HASH_META] = sha1($content);

		// 获取 intro
		$parts = preg_split('/^\s*<!--\s*more\s*-->\s*$/m',$content);
		if(isset($parts[1]))
		{
			$meta[Post::INTRO_META] = Markdown(substr($parts[0],strlen($all[0])));
		}

		COMPLETE_PARSE_META:


		return $meta;
	}

	/**
	 * @param $content
	 * @return string
	 */
	public function parseContentOnly($content)
	{
		return Markdown($content);
	}
}