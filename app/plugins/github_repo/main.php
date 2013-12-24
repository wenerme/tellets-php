<?php
include_once __DIR__.'/GitHubRepo.php';
use github_repo\GithubRepo;

Hook::AddHook(Hook::BOOTSTRAP, function()
{
	global $config;
	// ['github_repo'] 可能不存在,抑制该错误
	$setting = @$config['plugins']['github_repo'];

	if(is_null($setting))
	{
		$setting['enable'] = true;

		$config->addDefault('github_repo',$setting,<<<EOT
github_repo 插件:
bool enable 设置是否启用
array repos 添加github源

repo格式为 user/repo[:branch][/path][;auth=username|password][;readmeonly]
EOT
			,Config::NS_PLUGINS);
	}

	if(! $setting['enable'])
		return;

	Hook::AddHook(Hook::FIND_POST_LIST,function(&$list) use($setting)
	{
		$files = array();
		if(isset($setting['repos']))
		foreach($setting['repos'] as $repostring)
		{
			$repo = new GithubRepo($repostring);
			$files = array_merge($files, $repo->getCatchedFileList());
		}

		$list = array_merge($list, $files);

		// 删除其余文件
		$all = glob(GITHUB_CACHE_DIR.DIRECTORY_SEPARATOR.'*');
		$remove = array_diff($all,$files);
		array_walk($remove,function($fn){unlink($fn);});
		//var_dump($files,$all,$remove);
	});
});

