<?php

require_once 'IPostHelper.php';

function lazeLoadPostContent($post)
{
	return file_get_contents(CACHE_DIR.'/'.getPostCacheFileName($post));
}

class FilePostHelper implements IPostHelper
{

	/** @var Post[] */
	private $postList = array();

	private $changed = false;
	/**
	 * cache the $category result.
	 *
	 * @var string[]
	 */
	private $category = NULL;
	/**
	 * cache the $tag result.
	 *
	 * @var string[]
	 */
	private $tag = NULL;

	private $filename = '';
	/**
	 * @param $filename 存储meta数据的文件名
	 */
	public function __construct($filename)
	{
		$this->filename = $filename;
		if (false == file_exists($filename))
			return;

		$metalist = unserialize(file_get_contents($filename));

		foreach ($metalist as $meta)
		{
			$post = new Post();


			$post->setMetaDate($meta)
				 ->setContent('lazeLoadPostContent')
			;

			$this->postList[] = $post;
		}
	}
	public function  __destruct()
	{
		if($this->changed)
			$this->Save($this->filename);
	}
	/**
	 * 将数据保存到指定文件
	 * @param string $filename
	 */
	public function Save($filename)
	{
		// 根据日期排序
		usort($this->postList,function($a,$b){return $b['date'] - $a['date'];});

		$metas = array();
		foreach($this->postList as $post)
		{
			$metas[] = $post->getMetaDate();

			$content = $post->getContent();

			if(is_callable($content))
				continue;

			// cache content
			file_put_contents(CACHE_DIR.'/'.getPostCacheFileName($post),$content);
		}

		file_put_contents($filename,serialize($metas));
	}

	public function addPost($post)
	{
		if(!$post)
			throw new InvalidArgumentException("Post '$post' is invalid.");
		// 只有已发布的才能进行添加
		if($post->isPublished())
		{
			// 保持link不会改变
			if(isset($post['link']))
				$post['link'] = toLinkTitle($post['link']);
			else
				$post['link'] = toLinkTitle($post->getTitle());

			// 如果没有 date,则设置为 0,这样date会是最老的文章时间
			isset($post['date']) || $post['date'] = 0;

			$this->postList[] = $post;
			$this->changed = true;
		}


		return $this;
	}

	public function getPostList()
	{
		return $this->postList;
	}

	public function getCategoryList()
	{
		if ($this->category === NULL)
			goto DEAL_OVER;

		$category = array();

		foreach ($this->postList as $post)
			foreach ($post->getCategory() as $k => $v)
				$category[$v] = true;

		$this->category = array_keys($category);

		DEAL_OVER:

		return $this->category;
	}

	public function getTagList()
	{
		if ($this->tag === NULL)
			goto DEAL_OVER;

		$item = array();

		foreach ($this->postList as $post)
			foreach ($post->getTag() as $k => $v)
				$item[$v] = true;

		$this->tag = array_keys($item);

		DEAL_OVER:

		return $this->tag;
	}

	/**
	 * @return Post[]
	 */
	public function getPostListOfCategory($category)
	{
		$list = array();

		foreach ($this->postList as $post)
		{
			$categories = $post->getCategory();
			if (is_array($categories) && in_array($category, $categories))
				$list[] = $post;
		}
		return $list;
	}

	/**
	 * @return Post[]
	 */
	public function getPostListOfTag($tag)
	{
		$list = array();

		foreach ($this->postList as $post)
			if (@in_array($tag, $post->getTag()))
				$list[] = $post;

		return $list;
	}

	public function Clear()
	{
		if (count($this->postList) > 0)
		{
			$this->postList = array();
			$this->changed = true;
		}
	}

	/**
	 * 获取总的文章数量
	 * @return int
	 */
	public function getPostCount()
	{
		return count($this->postList);
	}
}