<?php


class Request
{
	protected $tags = null;
	protected $category = null;
	protected $title = null;
	/**
	 * @var Post
	 */
	protected $post = null;
	/**
	 * @var Post[]
	 */
	protected $posts = null;
	protected $action = null;
	protected $type = array();
	protected $pageNo = 1;

	/**
	 * @param string $params 请求路径,例如 /category/wener, /this-is-a-title, /page/2
	 */
	public function __construct($params)
	{
		$type = &$this->type;

		/*
		// Get the current page.
		$currentpage = @($_SERVER["HTTPS"] != 'on') ? 'http://' . $_SERVER["SERVER_NAME"] : 'https://' . $_SERVER["SERVER_NAME"];
		$currentpage .= $_SERVER["REQUEST_URI"];
		// page type
		$type['home'] = $currentpage == BLOG_URL;
		*/

		// 初始 type 为false
		foreach(explode('|','category|tag|action|single|pages|home') as $v)
			$type[$v] = false;

		preg_match('~(?<type>category|tag|action)\/(?<value>[^\/\\#&]+)~i', $params,$matches);

		if(isset($matches['type']) && isset($matches['value']))
		{
			if($type['category'] = $matches['type'] === 'category')
				$this->category = $matches['value'];
			elseif($type['tag'] = $matches['type'] === 'tag')
				$this->tags = $matches['value'];
			elseif($type['action'] = $matches['type'] === 'action')
				$this->action = $matches['value'];
		}

		// 对主页的判断 如果只有页号参数 则依然是主页
		$type['home'] = (bool)preg_match('#^(page\/(?<pageno>[0-9]+))?$#i', $params);


		$type['single'] = !($type['home'] || $type['category'] || $type['tag'] || $type['action']);
		$type['pages'] = $type['home'] || $type['category'] || $type['tag'];

		if($type['single'])
			$this->title = $params;

		preg_match('#page\/(?<pageno>[0-9]+)#i', $params, $matches);
		if(isset($matches['pageno']))
			$this->pageNo = $matches['pageno'];

		// 校正
		if(is_string($this->tags))
		{
			$this->tags = preg_split('#[,|]#',$this->tags);
			$this->tags = array_map('trim',$this->tags);
		}
	}

	public function getPageCount()
	{
		global $postHelper;

		return ceil($postHelper->getPostCount() / $this->getPostPrePage());
	}

	public function getPostPrePage()
	{
		global $config;
		return $config['posts_pre_page'];
	}
	protected function getter($fn)
	{
		preg_match('#(get|is)(?<name>.*)#i', $fn, $matches);
		$name = lcfirst($matches['name']);
		return $this->$name;
	}
	protected function typeGetter($fn)
	{
		preg_match('#(get|is)?(?<name>.*)#i', $fn, $matches);
		$name = lcfirst($matches['name']);
		return $this->type[$name];
	}

	public function getCategory()
	{return $this->getter(__FUNCTION__);}
	public function getTags()
	{return $this->getter(__FUNCTION__);}
	public function getTitle()
	{return $this->getter(__FUNCTION__);}
	public function getAction()
	{return $this->getter(__FUNCTION__);}
	public function getPageNo()
	{return $this->getter(__FUNCTION__);}

	public function isSingle()
	{return $this->typeGetter(__FUNCTION__);}
	public function isTag()
	{return $this->typeGetter(__FUNCTION__);}
	public function isCategory()
	{return $this->typeGetter(__FUNCTION__);}
	public function isHome()
	{return $this->typeGetter(__FUNCTION__);}
	public function isAction()
	{return $this->typeGetter(__FUNCTION__);}
	public function isPages()
	{return $this->typeGetter(__FUNCTION__);}

	public function hasNextPage()
	{
		return $this->isPages() && $this->pageNo < $this->getPageCount();
	}
	public function hasPrevPage()
	{
		return $this->isPages() && $this->pageNo >= 2;
	}
	public function getNextPageURL()
	{
		if(! $this->hasNextPage()) throw new Exception('no next page');
		$url = $this->getPageURL();
		$url = rtrim($url,'/');
		$url .= '/page/'.($this->pageNo + 1);
		return $url;
	}
	public function getPrevPageURL()
	{
		if(! $this->hasPrevPage()) throw new Exception('no prev page');
		$url = $this->getPageURL();
		$url = rtrim($url,'/');
		$url .= '/page/'.($this->pageNo - 1);
		return $url;
	}

	public function getPageURL()
	{
		$type = $this->type;
		$url = rtrim(BLOG_URL).'/';

		if($type['home'])
			;
		elseif($type['single'])
			$url .= $this->title;
		else{
			foreach(explode('|','category|tag|action') as $v)
				if($type[$v])
				{
					if($v === 'tag')
						$url .= $v.'/'.implode('|',$this->tags);
					else
						$url .= $v.'/'.$this->$v;
					break;
				}
		}

		return $url;
	}

	/**
	 * @return null|Post
	 */
	public function getSinglePost()
	{
		global $tellets;
		$post = &$this->post;
		if(is_null($post))
		{
			$post = $tellets->resolvePost($this->title);
		}
		return $post;
	}

	/**
	 * @return Post[]
	 */
	public function getPosts()
	{
		global $postHelper;
		$posts = &$this->posts;
		if(is_null($posts))
		{
			$posts = array();
			if($this->isHome())
				$posts = $postHelper->getPostList();
			elseif($this->isTag())
				$posts = $postHelper->getPostListOfTags($this->tags);
			elseif($this->isCategory())
				$posts = $postHelper->getPostListOfCategory($this->category);
			// 分页
			$start = ($this->getPageNo() - 1) * $this->getPostPrePage();
			$posts = array_slice($posts,$start,$this->getPostPrePage());
		}
		return $posts;
	}
}