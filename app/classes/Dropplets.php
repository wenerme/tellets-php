<?php
require_once LIB_DIR . '/phpass.php';

class Dropplets
{

	/**
	 * @var Config
	 */
	public $config;
	/**
	 * @var IPostHelper
	 */
	public $postHelper;

	public function __construct()
	{
	}

	/**
	 * 删除所有缓存文件
	 *
	 * 只有在登录后才有效
	 */
	public function Invalidate()
	{

	}

	/**
	 * 删除缓存,从新生成所有文章
	 *
	 * 只有在登录后才有效
	 *
	 * @return $this
	 */
	public function Update()
	{
		// 删除所有缓存的文章
		$files = glob(CACHE_DIR . '/*.post');
		foreach ($files as $file)
			if (is_file($file))
				unlink($file);

		$this->postHelper->Clear();

		// 生成所有文章
		$list = $this->getPostFileList();
		foreach ($list as $file)
		{
			echo("add post $file <br>\n");
			$this->postHelper->addPost(Post::ParseFile($file));
		}

		return $this;
	}

	public function SetUp()
	{
		$hasher = new PasswordHash(8, false);
		$this->config['password'] = $hasher->HashPassword($_POST["password"]);

		return $this;
	}

	/**
	 * 返回是否登录成功
	 *
	 * @param $password
	 * @return bool
	 */
	public function Login($password)
	{
		$hasher = new PasswordHash(8, false);

		if ($this->config['password'] != $hasher->HashPassword($password))
			return false;

		$_SERVER['user'] = true;

		return true;
	}

	public function Logout()
	{
		$_SERVER['user'] = NULL;
	}

	public function isLogin()
	{
		return !!$_SERVER['user'];
	}

	/**
	 * 获取文章文件列表
	 */
	public function getPostFileList()
	{
		$list = array();
		Hook::TriggerBeforeEvent(Hook::FIND_POST_LIST_EVENT, array($list));

		$list = array_merge($list, glob(POSTS_DIR . '/*'));

		Hook::TriggerAfterEvent(Hook::FIND_POST_LIST_EVENT, array($list));

		return $list;
	}

	public function resolvePost($name)
	{
		/**
		 * @var Post
		 */
		$post = NULL;
		Hook::TriggerBeforeEvent(Hook::RESOLVE_POST_EVENT, array(&$post, $name));

		// 只有当 $post 没有被解析的时候才进行
		if (!$post)
			foreach ($this->postHelper->getPostList() as $try)
			{
				switch ($name)
				{
					case $try->getMeta('link'):
					case $try->getMeta(Post::HASH_META):
					case $try->getMeta(Post::TITLE_META):
						$post = $try;
				}
				//
				if ($post)
					break;
			}

		Hook::TriggerAfterEvent(Hook::RESOLVE_POST_EVENT, array(&$post, $name));

		return $post;
	}
}