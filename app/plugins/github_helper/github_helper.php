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

class RepoHelper
{

	// static var
	private static $CacheDir;
	private static $DefaultFileCacheDir;
	private static $IsInit = false;

	private static $Users = array();
	private static $Branches = array();

	/**
	 * @var Repo
	 */
	private $repo = null;
	private $curl = null;
	/**
	 * Cache last response header, parse when needed.
	 * @var String
	 */
	private $lastHeader = null;

	private $rateLimit = null;
	private $rateLimitRemaining = null;
	private $rateLimitReset = null;

	private $branch = null;
	private $tree = null;
	private $fileCacheDir = null;
	/**
	 * Static Init
	 */
	private static function Init()
	{
		if(self::$IsInit)
			return;
		self::$IsInit = true;
		// cache dir
		self::$CacheDir = CACHE_DIR.DIRECTORY_SEPARATOR.__NAMESPACE__;
		self::$DefaultFileCacheDir = self::$CacheDir.DIRECTORY_SEPARATOR.'files';
		is_dir(self::$CacheDir) || mkdir(self::$CacheDir, 0777, true);
		is_dir(self::$CacheDir) || mkdir(self::$DefaultFileCacheDir, 0777, true);

		// load caches
		//$user_data_fn = self::$CacheDir.DIRECTORY_SEPARATOR.'user.data';
		//if(file_exists($user_data_fn))
		//	self::$Users = json_decode(file_get_contents($user_data_fn), true);

		$branch_data_fn = self::$CacheDir.DIRECTORY_SEPARATOR.'branch.data';
		if(file_exists($branch_data_fn))
			self::$Branches = json_decode(file_get_contents($branch_data_fn), true);

		// save catches
		register_shutdown_function(function() use($branch_data_fn)
		{
			//file_put_contents($user_data_fn, json_encode(RepoHelper::$Users));
			//file_put_contents($branch_data_fn, json_encode(RepoHelper::$Branches));
		});
	}

	public  function __construct($repo)
	{
		if(false == ($repo instanceof Repo))
			throw new \InvalidArgumentException('$repo parameter should be an instance of Repo');
		self::Init();// Only init when this class instanced.

		$this->fileCacheDir = self::$DefaultFileCacheDir;

		$this->repo = $repo;
		$curl = &$this->curl;

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_USERAGENT,"User-Agent: tellets/".TELLETS_VERSION);
		curl_setopt($curl, CURLOPT_HEADER, true);// will get the header info
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		if(boolval($this->repo->getAuth()))
		{
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, $this->repo->getAuth());
		}
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);// we dont have cert

	}

	public function __destruct()
	{
		curl_close($this->curl);
	}

	public function getResponse($url, $decode = true)
	{
		$curl = &$this->curl;

		curl_setopt($curl, CURLOPT_URL, $url);
		$result = curl_exec($curl);
		if(false == $result)
			throw new GitHubException(curl_error($curl));

		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$this->lastHeader = substr($result, 0, $header_size);
		$body = substr($result, $header_size);
		// reset all
		$this->rateLimit = $this->rateLimitRemaining = $this->rateLimitReset = null;

		//
		if(!$decode)
			return $body;

		$res = json_decode($body, true);
		if (isset($res['message']))
			throw new GitHubException($res['message']);

		return $res;
	}

	/**
	 * https://api.github.com/users/:user/repos
	 * @return mixed
	 */
	public function getUserRepos()
	{
		$user = $this->repo->getUser();
		$item = &self::$Users[$user];

		if($item)
			return $item;

		$url = "https://api.github.com/users/{$user}/repos";
		$item = $this->getResponse($url);

		return $item;
	}
	public function getBranches()
	{
		$user_repo = $this->repo->getUser().'/'.$this->repo->getRepo();
		$item = &self::$Branches[$user_repo];

		if($item)
			return $item;

		$url = 'https://api.github.com/repos/'.$user_repo.'/branches';
		$item = $this->getResponse($url);

		return $item;
	}
	public function getBranch()
	{
		$branch = &$this->branch;
		if(!is_null($branch))
			return $branch;

		$branches = $this->getBranches();
		$branchName = $this->repo->getBranch();

		$item = null;
		foreach($branches as $k)
			if($k['name'] === $branchName)
			{
				$item = $k;
				break;
			}

		if(is_null($item))
			throw new GitHubException("Branch '$branchName' not found");

		// detect cache
		$fn = self::$CacheDir.DIRECTORY_SEPARATOR.$item['commit']['sha'].'.branch';
		if(file_exists($fn))
			$content = file_get_contents($fn);
		else
		{
			// make a request
			$url = 'https://api.github.com/repos/'.$this->repo->getUserRepo().'/branches/'.$this->repo->getBranch();
			$content = $this->getResponse($url,false);
			file_put_contents($fn, $content);
		}
		$branch = json_decode($content, true);

		DONE:
		return $branch;
	}

	/**
	 * Array of item in tree
	 * @return array
	 */
	public function getTree($filter = null)
	{
		$tree = &$this->tree;
		if(!is_null($tree))
			return $tree;

		$commit = $this->getBranch();
		$sha = $commit['commit']['commit']['tree']['sha'];
		$url = $commit['commit']['commit']['tree']['url'];
		$url .= '?recursive=1';

		$fn = self::$CacheDir.DIRECTORY_SEPARATOR."$sha.tree";

		if(file_exists($fn))
			$content = file_get_contents($fn);
		else
		{
			$content = $this->getResponse($url,false);
			file_put_contents($fn, $content);
		}

		$item = json_decode($content, true);
		$tree = $item['tree'];

		// if need filter
		if(!is_null($filter))
			return array_filter($tree, $filter);

		return $tree;
	}

	public function getTreeInPath($path = null)
	{
		if(is_null($path))
			$path = $this->repo->getPath();
		$tree = $this->getTree();
		if($path)
			$tree = array_filter($tree, function($item)use($path)
			{
				return 0 === strpos($item['path'],$path);
			});
		return $tree;
	}

	public function getLocalBlob($item, $ignore_type = true)
	{
		if(! $ignore_type && $item['type'] !== 'blob')
			throw new GitHubException('itme type must be blob');

		$ext = pathinfo($item['path'],PATHINFO_EXTENSION);
		$fn = $this->fileCacheDir.DIRECTORY_SEPARATOR."$item[sha].$ext";
		// detect cache
		if(file_exists($fn))
			goto DONE;

		$response = $this->getResponse($item['url']);
		file_put_contents($fn, base64_decode($response['content']));

		DONE:
		return $fn;
	}
	public function getAllLocalBlob($items)
	{
		$files = array();
		if(! is_array($items))
			$items = array($items);

		foreach($items as $item)
			if($item['type'] === 'blob')
				$files[] = $this->getLocalBlob($item);

		return $files;
	}
	public function removeLocalBlob($item)
	{
		if($item['type'] !== 'blob')
			throw new GitHubException('itme type must be blob');

		$ext = pathinfo($item['path'],PATHINFO_EXTENSION);
		$fn = $this->fileCacheDir.DIRECTORY_SEPARATOR."$item[sha].$ext";
		// detect cache
		if(file_exists($fn))
			unlink($fn);
	}
	public function getBlobContent($item)
	{
		file_get_contents($this->getLocalBlob($item));
	}
	private function parseHeader()
	{
		if(is_null($this->lastHeader))
			$this->getResponse('https://api.github.com/rate_limit');
		$pattern = '~
X-RateLimit-
(?:
(?:Limit:(?<limit>[0-9\s]+))
|(?:Remaining:(?<remaining>[0-9\s]+))
|(?:Reset:(?<reset>[0-9\s]+))
)+~x';
		preg_match_all($pattern, $this->lastHeader, $matches);

		// reduce to one
		$values = array();
		foreach(explode('|','limit|remaining|reset') as $k)
			if(boolval($matches[$k]))
			{
				// to void dereference
				$v = array_values(array_filter($matches[$k]));
				$values[$k] = $v[0];
			}

		//
		$this->rateLimit = intval($values['limit']);
		$this->rateLimitRemaining = intval($values['remaining']);
		$this->rateLimitReset = intval($values['reset']);
	}

	public function getRateLimitRemaining()
	{
		if(is_null($this->rateLimitRemaining))
			$this->parseHeader();
		return $this->rateLimitRemaining;
	}
	public function getRateLimit()
	{
		if(is_null($this->rateLimit))
			$this->parseHeader();
		return $this->rateLimit;
	}
	public function getRateLimitReset()
	{
		if(is_null($this->rateLimitReset))
			$this->parseHeader();
		return $this->rateLimitReset;
	}
	public function getFileCacheDir()
	{
		return $this->fileCacheDir;
	}
	public function setFileCacheDir($dir)
	{
		if(!is_dir($dir))
			throw new GitHubException('Cache dir not exists.');
		$this->fileCacheDir = $dir;
	}
}

class GitHubException extends \Exception
{
	public function __construct($message, $code = 0)
	{
		parent::__construct($message, $code);
	}
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
(?:/(?<path>[^;]*))? # path optional
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
			if(boolval($option_matches[$k][0]))// 确保至少有一个值有效
			{
				// to void dereference
				$values = array_values(array_filter($option_matches[$k]));
				$this[$k] = $values[0];
			}else
				$this[$k] = '';

		// setup default
		global $config;
		$setting = &$config[\Config::NS_PLUGINS]['github_helper'];

		if(! $this['branch'])
			$this['branch'] = 'master';

		if(! $this['auth'])
			$this['auth'] = $setting['auth'];

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
	public function getUserRepo()
	{
		return "$this[user]/$this[repo]";
	}
}
//</editor-fold>

/* ITEM
"mode": "100755",
"type": "blob",
"sha": "7593d4bb1a5758736712cc9f0eed1091f6545507",
"path": ".gitignore",
"size": 99,
"url": "https://api.github.com/repos/wenerme/blog/git/blobs/7593d4bb1a5758736712cc9f0eed1091f6545507"
*/