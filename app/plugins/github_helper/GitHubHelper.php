<?php
namespace github_helper;

/**
 * Class Helper
 *
 * 缓存一个用户的所有 branch,获取branch的时候,
 * 才好判断branch是否有变化,
 * 如果没有变化,则直接使用缓存的 tree list.
 *
 * 需要缓存的东西有
 * User Branch List
 * Branch Tree List
 * 匹配的文件,需要进行缓存
 *
 * 需要下载的东西有
 * User Branch List/只会下载一次
 * Branch Tree List/只有检测到新的 Branch sha 时才会获取 然后更新本地缓存.
 * 匹配文件/当匹配的sha没有本地缓存时进行获取
 *
 * 需要删除的东西有
 *
 * 每次运行时,记录使用到的文件名
 * 结束时,对比,删除本地存在的不一样的文件
 *
 * 配置
 * auth 默认的auth
 *
 * catch 命名策略
 *
 * User Branch List: user.branches
 * Branch Tree List: branch_sha.tree
 * File : file/sha.ext
 */



class CacheHelper
{
	private $CacheDir;
	private $FileCacheDir;

	private static $instance = null;

	private function __construct()
	{
		$this->CacheDir = CACHE_DIR.DIRECTORY_SEPARATOR.__NAMESPACE__;
		$this->FileCacheDir = $this->CacheDir.DIRECTORY_SEPARATOR.'files';
		mkdir($this->CacheDir, 0777, true);
		mkdir($this->FileCacheDir, 0777, true);
	}

	/**
	 * Get the instance of cache helper.
	 *
	 * @return CacheHelper
	 */
	public static  function help()
	{
		if(self::$instance == null)
			self::$instance = new self();
		return self::$instance;
	}
}

class RepoHelper
{
	private $context;
	private $options;
	/**
	 * @var Repo
	 */
	private $repo = null;

	public  function __construct($repo)
	{
		if(false == ($repo instanceof Repo))
			throw new \InvalidArgumentException('$repo parameter should be an instance of Repo');
		$this->repo = $repo;
		$this->options = array(
			'http'=>array(
				'method'=>"GET",
				'header'=>
					"User-Agent: tellets/1.0\r\n"
			)
		);
	}

	public function getResponse($url)
	{}

	public function isRateLimitReached()
	{}

}

/**
 * Class Repo represents a repo setting
 *
 * @package github_helper
 */
//<editor-fold desc="Repo">
class Repo extends \ArrayObject
{
	/**
	 * format: user/repo[:branch][/path][;auth=user|password]
	 *
	 * @param string $repo_string
	 */
	public function __construct($repo_string)
	{
		// Match basic
		$basic_pattern = '~
(?<user>[^/]+) # user name require
(?:/(?<repo>[^/:]+)) # repo require
(?::(?<branch>[^/]+))? # branch optional
(?:/(?<path>.*))? # path optional
		~x';
		$repo_string = trim($repo_string);
		preg_match($basic_pattern, $repo_string, $basic_matches);

		foreach (explode('|', 'user|repo|branch|path') as $k)
			$this[$k] = isset($basic_matches[$k])? $basic_matches[$k]: '';

		// Match options
		$option_pattern = '~
(?:;
(?:auth=(?<auth>[^;]+)) # for auth
|(?:(?<readmeonly>readmeonly))  # Only care about readme
)+ # match option mutiltimes
		~x';

		$option_string = substr($repo_string, strlen($basic_matches[0]));
		preg_match_all($option_pattern, $option_string, $option_matches);

		foreach(explode('|','auth|readmeonly') as $k)
			if(isset($matches[$k]) && $matches[$k])
			{
				// 使用 $values 来避免 dereference
				$values = array_values(array_filter($matches[$k]));
				$this[$k] = $values[0];
			}
	}

	protected function getter($fn)
	{
		preg_match('#(?:get|is)(?<name>.*)#i', $fn, $matches);
		// name conversion
		//$name = lcfirst($matches['name']);
		$name = strtolower($matches['name']);
		return $this[$name];
	}

	public function getAuth()
	{return $this->getter(__FUNCTION__);}
	public function getRepo()
	{return $this->getter(__FUNCTION__);}
	public function getBranch()
	{return $this->getter(__FUNCTION__);}
	public function getUser()
	{return $this->getter(__FUNCTION__);}
	public function getPath()
	{return $this->getter(__FUNCTION__);}
	public function isReadMeOnly()
	{return $this->getter(__FUNCTION__);}
}
//</editor-fold>


function get_user_branch_list($username)
{}

function get_user_branch($username, $branch)
{}

function get_tree_of_branch($sha)
{}

function get_response()
{}

function get_file_by_sha()
{
	// 判断本地是否存在
}