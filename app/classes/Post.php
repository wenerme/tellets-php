<?php
require_once(APP_DIR.'./library/markdown.php');

class Post extends ArrayObject
{
	const AUTHOR_META = 'author';
	const TITLE_META = 'title';
	const CATEGORY_META = 'category';
	const TAG_META = 'tag';
	const DATE_META = 'date';
	const INTRO_META = 'intro';
    const STATE_META = 'state';
    const HASH_META = 'hash';
	
	//private $meta = array();
	private $content = '';


	
	public function getAuthor(){return @$this[self::AUTHOR_META];}
	public function getTitle(){return @$this[self::TITLE_META];}
    /** @return string[] */
	public function getCategory(){return @$this[self::CATEGORY_META];}
    /** @return string[] */
	public function getTag(){return @$this[self::TAG_META];}
    /** @return int the date is unix timestamp. */
	public function getDate(){return @$this[self::DATE_META];}
	public function getIntro(){return @$this[self::INTRO_META];}

    public function getState(){return @$this[self::STATE_META];}

    public function isPublished(){return $this->getState() === 'published';}

	public function getContent()
    {
        // allowed lazy load content
        $content = $this->content;
        if(is_callable($content))
            $this->content = $content($this);

        return $this->content;
    }

    /**
     * @param (string|callable) $content can be a callable to implement laze load.
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

	/**
	 * @return array
	 */
	public function getMetaDate(){return parent::getArrayCopy();}
	public function setMetaDate($meta){parent::exchangeArray($meta);return $this;}
	
	public function getMeta($name){return @$this[$name];}
	public function setMeta($name, $value){$this[$name] = $value;return $this;}
	/**
	 * 解析 markdown，生成一篇文章
	 * @param string $markedown
	 */
	public static function ParseContent($markedown)
	{
		$post = new Post();

		Hook::TriggerBeforeEvent(Hook::PARSE_POST_EVENT,array($post,$markedown));

		// 解析元字段
		$meta = array();
		
		// match all meta out
		preg_match('#(^\s*<!--([^:]+):(.*)-->\s*$[\n\r]+)+#m',$markedown,$all);
		
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
        isset($meta[self::CATEGORY_META]) && $meta[self::CATEGORY_META] = self::WithMultiValue($meta[self::CATEGORY_META]);
        isset($meta[self::TAG_META]) && $meta[self::TAG_META] = self::WithMultiValue($meta[self::TAG_META]);

        // parse date
        isset($meta[self::DATE_META]) && $meta[self::DATE_META] = strtotime($meta[self::DATE_META]);

        // state
        isset($meta[self::STATE_META]) || $meta[self::STATE_META] = 'draft';

        COMPLETE_PARSE_META:
		
		// 获取 intro
        $parts = preg_split('/^\s*<!--\s*more\s*-->\s*$/m',$markedown);
        if(isset($parts[1]))
            $meta[self::INTRO_META] = Markdown($parts[0]);

		// 内容
        $post->content = Markdown($markedown);

        // 生成 sha1
        $meta[self::HASH_META] = sha1($markedown);

        // 处理完成
        $post->setMetaDate($meta);

		Hook::TriggerAfterEvent(Hook::PARSE_POST_EVENT,array($post,$markedown));
        return $post;
	}

    /**
     * 解析文件,生成一篇文章
     * @param $filename
     * @return Post
     */
    public static function ParseFile($filename)
	{ return self::ParseContent(file_get_contents($filename));}

    /**
     * Convert the string to array.
     * @param string $value
     * @return string
     */
    public static function WithMultiValue($value)
    {
        $item = preg_split("#[,|]#",$value);
        $item = array_map(function($v){return trim($v);}, $item);
        return $item;
    }
}


