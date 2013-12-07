<?php

require_once 'IPostHelper.php';

class FilePostHelper implements IPostHelper
{

	const FILENAME_META = 'filename';

	/** @var Post[] */
	private $postList = array();

    private $changed = false;
	/**
	 * cache the $category result.
	 * @var string[]
	*/
	private $category = null;
	/**
	 * cache the $tag result.
	 * @var string[]
	 */
	private $tag = null;

    /**
     * @param $filename 存储meta数据的文件名
     */
    public function __construct($filename)
    {
        if(false == file_exists($filename))
            return;

        $metalist = unserialize($filename);

        foreach($metalist as $meta)
        {
            $post = new Post();
            $post->setMetaDate($meta)->setContent(array(__CLASS__,'lazeLoadPostContent'));

            $this->postList[] = $post;
        }
    }
	private static function lazeLoadPostContent($post)
	{

	}
    public function addPost($post)
    {
        $this->postList[] = $post;
        $this->changed = true;
    }
	public function getPostList()
	{
		return $this->postList;
	}

	public function getCategoryList()
	{
		if($this->category === null)
			goto DEAL_OVER;

		$category = array();

		foreach($this->postList as $post)
		foreach($post->getCategory() as $k => $v)
			$category[$v] = true;

		$this->category = array_keys($category);

		DEAL_OVER:
		return $this->category;
	}

	public function getTagList()
	{
		if($this->tag === null)
			goto DEAL_OVER;

		$item = array();

		foreach($this->postList as $post)
		foreach($post->getTag() as $k => $v)
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

		foreach($this->postList as $post)
		if(in_array($category, $post->getCategory()))
			$list[] = $post;

		return $list;
	}
	/**
	 * @return Post[]
	 */
	public function getPostListOfTag($tag)
	{
		$list = array();

		foreach($this->postList as $post)
		if(in_array($tag, $post->getTag()))
			$list[] = $post;

		return $list;
	}

    public function Clear()
    {

    }
}