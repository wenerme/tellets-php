<?php
require_once(LIB_DIR.'markdown.php');

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
    /** @return string */
	public function getCategory(){return @$this[self::CATEGORY_META];}
    /** @return string[] */
	public function getTags()
	{
		$tags = isset($this[self::TAG_META])? $this[self::TAG_META]: array();
		return is_array($tags)?$tags:array($tags);
	}
    /** @return int the date is unix timestamp. */
	public function getDate(){return @$this[self::DATE_META];}
	public function getIntro(){return @$this[self::INTRO_META];}
	public function getIntroOrContent(){return isset($this[self::INTRO_META])? $this[self::INTRO_META]:$this->getContent();}

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
}


