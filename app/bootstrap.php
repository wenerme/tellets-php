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
include_once CLASSES_DIR . "/functions.php";
include_once LIB_DIR . "/password.php";

define('BLOG_URL', get_app_url());
// load plugins
foreach (glob(APP_DIR . "/plugins/*/main.php") as $filename)
{
	include $filename;
}

// 全局的配置
$config = new Config(DATA_DIR . '/config.php');

// setup default config
{
	$config->addDefault('timezone', 'Asia/Chongqing', <<<EOT
设置时区,以正确的计算时间.
请参考:http://www.php.net/manual/zh/timezones.php
EOT
	);


	$config->addDefault('author', 'wener', <<<EOT
作者名,当在启用多作者模式后,该值会作为 author 的回滚值.
EOT
	);
	$config->addDefault('author_email', 'wenermail@gmail.com', <<<EOT
作者邮箱,当在启用多作者模式后,该值会作为 author_email 的回滚值.
EOT
	);
	$config->addDefault('author_site', 'http://wener.me', <<<EOT
作者主页,当在启用多作者模式后,该值会作为 author_site 的回滚值.
EOT
	);

	$config->addDefault('blog_title', 'One world, one wener.');

	$config->addDefault('meta_description', 'One world, one wener.');

	$config->addDefault('language', 'zh-cn','设置你使用的语言代码');



	$config->addDefault('posts_pre_page', 6);
	$config->addDefault('date_format', '%Y/%m/%d', 'Set the time format, see http://strftime.net/');

	$config->addDefault('intro_title', 'Wener');
	$config->addDefault('intro_text', 'This place, where you can stay.');

	$config->addDefault('enable_multi_author', true, <<<EOT
多作者模式,即可以在文章内启用多作者
EOT
);

	$config->addDefault('feed_max_items',10,'Feed 里显示文章的数量');

	// 模板相关设置
	$config->addDefault('extra_links', array(
		'GitHub' => 'https://github.com/WenerLove/tellets',
		'Get Start' => 'https://github.com/WenerLove/tellets',
	),'额外链接,显示需要模板支持.', Config::NS_TEMPLATE);

	$config->addDefault('active', 'default','当前使用的模板,值为templates里文件夹名'
		, Config::NS_TEMPLATE);

	$config->addDefault('with_header','','将会附加在 生成页面的 header 中'
		, Config::NS_TEMPLATE);

	$config->addDefault('with_footer','','将会附加在 生成页面的 footer 中'
		, Config::NS_TEMPLATE);

	// 评论相关
	$config->addDefault('comment_type','disqus',<<<EOT
启用的社交评论插件,如果为 null|false则不启用
可能的值为 disqus 或 duoshuo 等,在默认模板里面支持这两个
设置该值后需要设置 comment_user
注意: 评论插件的显示需要模板支持.
EOT
	,Config::NS_TEMPLATE);

	$config->addDefault('comment_user','wener',<<<EOT
当设置 with_comments 启用评论插件后
使用该值来设置绑定的用户
EOT
	,Config::NS_TEMPLATE);

}

// 配置完成
Hook::TriggerEvent(Hook::CONFIG_COMPLETE,array($config));
// 使用配置来设置环境
{
	date_default_timezone_set($config['timezone']);
	ini_set('display_errors', true);

	define('BLOG_TITLE', $config['blog_title']);
	define('INTRO_TITLE', $config['intro_title']);
	define('INTRO_TEXT', $config['intro_text']);

	define('TEMPLATE_DIR', DATA_DIR . '/templates/' . $config[Config::NS_TEMPLATE]['active'] . '/');
	define('TEMPLATE_URL', BLOG_URL. '/data/templates/' . $config[Config::NS_TEMPLATE]['active'] . '/');

	// 似乎无效, 在 5.3 以上设置应该是可以用的
	// http://php.net/manual/zh/ini.core.php
	ini_set('short_open_tag', 1);
}


$postHelper = new FilePostHelper(CACHE_DIR . '/post.meta');
$tellets = new Tellets();
$tellets->config = & $config;
$tellets->postHelper = & $postHelper;

// 添加预定义动作
include_once CLASSES_DIR.'/predefined_hooks.php';
// 添加预定义解析器
ParserFactory::RegisterParser(MarkdownParser::EXTENSION, 'MarkdownParser');

// 启动完成
Hook::TriggerEvent(Hook::BOOTSTRAP,array());