<?php

class Tellets
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
	 * 只有在登录后才有效,但是当前尚未实现登录
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function Update()
	{
		global $postHelper;
		// 删除所有缓存的文章
		$files = glob(CACHE_DIR . '/*.post');
		foreach ($files as $file)
			if (is_file($file))
				unlink($file);

		$postHelper->Clear();

		// 生成所有文章
		$list = $this->getPostFileList();
		foreach ($list as $file)
		{
			if(mb_check_encoding($file, 'gbk'))
				echo "add post ",mb_convert_encoding($file, 'utf-8','gbk')," <br>\n";

			$post = ParserFactory::TryParseFile($file);
			if($post)
				$postHelper->addPost($post);
			else
				throw new Exception("Parse file '$file' to post filed.");
		}

		return $this;
	}

	public function SetUp()
	{
		global $config;
		$config['password'] = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $this->Update();
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
		global $config;
		if (!password_verify($password,$config['password']))
			return false;

		$_SERVER['user'] = true;

		return true;
	}

	public function Logout()
	{
		$_SERVER['user'] = NULL;
	}

	public function isLoggedIn()
	{
		return !!$_SERVER['user'];
	}

	/**
	 * 获取文章文件列表
	 */
	public function getPostFileList()
	{
		$list = array();

		Hook::TriggerEvent(Hook::FIND_POST_LIST, array(&$list));

		return $list;
	}

	public function resolvePost($name)
	{
		/**
		 * @var Post
		 */
		$post = NULL;

		Hook::TriggerEvent(Hook::RESOLVE_POST, array(&$post, $name));

		return $post;
	}
}
