<?php

use github_helper\Repo;
use github_helper\RepoHelper;
use github_helper\GitHubException;

Hook::AddHook(Hook::BOOTSTRAP, function()
{
	global $config;
	// ['github_repo'] 可能不存在,抑制该错误
	$setting = @$config['plugins']['github_repo'];

	if(is_null($setting))
	{
		$setting['enable'] = true;
        $setting['repos'] = 'wenerme/tellets/README.md';

		$config->addDefault('github_repo',$setting,<<<EOT
github_repo 插件:
使 tettels 可以使用GitHub作为文章源
设置
	enable  是否启用该插件
	repos    该值为数组,可添加多个

具体的 repo 格式请参见 github_helper
EOT
			,Config::NS_PLUGINS);
	}

	if(! $setting['enable'])
		return;

	$cache_dir = CACHE_DIR.DIRECTORY_SEPARATOR.'github_repo';
	try_mkdir($cache_dir);

	Hook::AddHook(Hook::FIND_POST_LIST,function(&$list) use($setting,$cache_dir)
	{
		$files = array();
		if(isset($setting['repos']))
		foreach($setting['repos'] as $repostring)
		{
			$repo = new Repo($repostring);
			$helper = new RepoHelper($repo);
			$helper->setFileCacheDir($cache_dir);

			try{
				$tree = $helper->getTreeInPath();
				$tree = array_filter($tree, function($item)
				{
					return $item['type'] === 'blob'
						&& ParserFactory::CanParse($item['path']);
				});

				$files = array_merge($files, $helper->getAllLocalBlob($tree));
			}catch (GitHubException $ex)
			{
				throw $ex;
			}
		}

		$list = array_merge($list, $files);

		// 删除其余文件
		$all = glob($cache_dir.DIRECTORY_SEPARATOR.'*');
		$remove = array_diff($all,$files);
		array_walk($remove,function($fn){unlink($fn);});
		//var_dump($files,$all,$remove);
	});
});

