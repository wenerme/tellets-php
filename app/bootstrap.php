<?php

define('TELLETS_VERSION','1.2');

define('APP_DIR', __DIR__);
define('TELLETS_DIR', realpath(__DIR__.'/../'));
define('DATA_DIR', TELLETS_DIR . '/data');
define('CACHE_DIR', DATA_DIR.DIRECTORY_SEPARATOR.'cache');
define('POSTS_DIR', DATA_DIR.DIRECTORY_SEPARATOR.'posts');

define('LIB_DIR', APP_DIR.DIRECTORY_SEPARATOR.'lib');
define('CLASSES_DIR', APP_DIR.DIRECTORY_SEPARATOR.'classes');

// 配置自动加载
set_include_path(get_include_path().PATH_SEPARATOR.LIB_DIR);
set_include_path(get_include_path().PATH_SEPARATOR.CLASSES_DIR);
spl_autoload_extensions('.php');
spl_autoload_register(function($name)
{
	spl_autoload($name);
	if(class_exists($name))
		return;
	$paths = explode(PATH_SEPARATOR, get_include_path());
	foreach($paths as $path)
	{
		$fn = $path.DIRECTORY_SEPARATOR.$name.'.php';
		if(file_exists($fn))
		{
			//echo "Found $name at $fn\n";
			include_once $fn;
			return;
		}
	}
	throw new Exception("Load $name failed.");
});

// 加载辅助函数
include_once LIB_DIR . "/password.php";
include_once CLASSES_DIR . "/functions.php";
include_once CLASSES_DIR . "/setup_env.php";

setup_encoding();

define('BLOG_URL', get_app_url());
// load plugins
foreach (glob(APP_DIR . "/plugins/*/main.php") as $filename)
{
	include $filename;
}

// 全局的配置
$config = new Config(DATA_DIR . '/config.php');

setup_default_config();

// 配置完成
Hook::TriggerEvent(Hook::CONFIG_COMPLETE,array($config));

// 使用配置来设置环境
setup_env_by_config();


$postHelper = new FilePostHelper(CACHE_DIR . '/post.meta');
$tellets = new Tellets();


// 添加预定义动作
include_once CLASSES_DIR.'/predefined_hooks.php';
// 添加预定义解析器
ParserFactory::RegisterParser(MarkdownParser::EXTENSION, 'MarkdownParser');

// 启动完成
Hook::TriggerEvent(Hook::BOOTSTRAP,array());