<?php
namespace github_helper;

include_once __DIR__.'/github_helper.php';

\Hook::AddHook(\Hook::BOOTSTRAP, function()
{
	global $config;
	//  可能不存在,抑制该错误
	$setting = @$config[\Config::NS_PLUGINS][__NAMESPACE__];

	if(is_null($setting))
	{
		$setting['auth'] = '';
		$config->addDefault(__NAMESPACE__,$setting,<<<EOT
github_helper 辅助插件:
设置:
	auth 作为访问 github 的授权,使用 BasicAuth
		 格式为 username:password

repo格式为 user/repo[:branch][/path][;auth=username:password][;readmeonly]
EOT
			,\Config::NS_PLUGINS);
	}

});