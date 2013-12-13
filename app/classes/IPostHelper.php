<?php

interface IPostHelper
{
	/**
	 * @return Post[]
	 */
	public function getPostList();
    public function getCategoryList();
    public function getTagList();
	/**
	 * @return Post[]
	 */
    public function getPostListOfCategory($category);
	/**
	 * @return Post[]
	 */
    public function getPostListOfTag($tag);

	/**
	 * @param Post $post
	 * @return $this
	 */
	public function addPost($post);
    /**
     * 清除所有 post, 在需要从新生成
     * @return $this
     */
    public function Clear();

	/**
	 * 获取总的文章数量
	 * @return int
	 */
	public function getPostCount();
}
