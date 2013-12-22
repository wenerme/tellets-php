<?php
namespace github_repo;

define("GITHUB_CACHE_DIR", CACHE_DIR.DIRECTORY_SEPARATOR.'github_repo');

if(!file_exists(GITHUB_CACHE_DIR))
	mkdir(GITHUB_CACHE_DIR);

class GithubRepo
{
	protected $prop = array();
	protected $last_error = null;
	protected $tree = null;
	protected $result_tree = null;
	protected  $context;
	private $track = false;
	/**
	 * setting format: vender/repo[:branch][/path]
	 */
	function __construct($repo)
	{
		preg_match('#(?<repo>[^/]+/[^/:]+)(:(?<branch>[^/]+))?(/(?<path>.*))?#', $repo, $matches);

		$prop = & $this->prop;
		foreach (explode('|', 'repo|branch|path') as $k)
			$prop[$k] = isset($matches[$k])? $matches[$k]: '';

		$prop['branch'] || $prop['branch'] = 'master';
		isset($prop['path']) || $prop['path'] = '';

		// 匹配其余参数 auth
		preg_match_all('~(?:;
		(?:auth=(?<auth>[^;]+))
		# |(?:(?<readmeonly>readmeonly)) # 暂时不实现这个功能
		)+~x', $repo, $matches);
		foreach(explode('|','auth|readmeonly') as $k)
		{
			if(isset($matches[$k]) && $matches[$k])
			{
				// 使用 $values 来避免 dereference
				$values = array_values(array_filter($matches[$k]));
				$prop[$k] = $values[0];
			}
		}

		$options = array(
			'http'=>array(
				'method'=>"GET",
				'header'=>
					"User-Agent: tellets/1.0\r\n"
			)
		);

		if(isset($prop['auth']))
			$options['http']['header'] .= "Authorization: Basic ".base64_encode($prop['auth'])."\r\n";

		$context = stream_context_create($options);
		$this->context = $context;
	}
	public function getTree()
	{
		if(! is_null($this->tree))
			return $this->tree;

		$prop = $this->prop;
		$url = "https://api.github.com/repos/{$prop['repo']}/branches/{$prop['branch']}";
		$rep = $this->getResponse($url);

		$this->noError();

		$url = $rep['commit']['commit']['tree']['url'];
		$url .= '?recursive=1';
		$rep = $this->getResponse($url);

		$this->noError();

		$tree = &$this->tree;
		$tree = $rep['tree'];

		$path = $prop['path'];
		$tree = array_filter($tree, function($item) use($path)
		{
			$result = $item['type'] === 'blob';

			if($result && strlen($path) > 0)
				$result = strpos($item['path'], $path) === 0;
			if($result)
				$result = \ParserFactory::CanParse($item['path']);

			return $result;
		});

		return $this->tree;
	}
	public function getFileURLList()
	{

	}

	public function getCatchedFileList()
	{
		$tree = $this->getTree();
		$list = array();
		foreach($tree as $item)
		{
			$ext = pathinfo($item['path'],PATHINFO_EXTENSION);
			$fn = GITHUB_CACHE_DIR.DIRECTORY_SEPARATOR."{$item['sha']}.$ext";
			$list[] = $fn;
			if(file_exists($fn))
				continue;
			$res = $this->getResponse($item['url']);
			file_put_contents($fn, base64_decode($res['content']));
		}

		return $list;
	}

	function getResponse($url)
	{
		$res = json_decode(file_get_contents($url, false, $this->context), true);
		if (isset($res['message']))
		{
			$this->last_error = 'ERROR:'.$res['message'];
			return NULL;
		}

		return $res;
	}

	protected function noError()
	{
		if(!is_null($this->last_error))
			throw new \Exception($this->last_error);
	}
	protected function getLastError()
	{
		return $this->last_error;
	}
	public function withTrack($track)
	{
		$this->track = $track;
	}
}