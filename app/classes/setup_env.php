<?php

function setup_encoding()
{
	// setup encoding
	mb_internal_encoding('UTF-8');

	$detect_order = mb_detect_order();
	$detect_order = array_merge($detect_order, explode('|','ASCII|ISO-8859-1|UTF-8|GBK'));
	$detect_order = array_unique($detect_order);

	mb_detect_order($detect_order);
}

function setup_default_config()
{
	global $config;

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

function setup_env_by_config()
{
	global $config;

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