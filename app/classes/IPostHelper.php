<?php

interface IPostHelper
{
    public function getPostList();
    public function getCategoryList();
    public function getTagList();

    public function getPostListOfCategory($category);
    public function getPostListOfTag($tag);

    /**
     * 清除所有 post, 在需要从新生成
     * @return $this
     */
    public function Clear();
}
