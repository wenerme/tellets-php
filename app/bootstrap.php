<?php

/**
 * The app dir
 */
define('APP_DIR', __DIR__);
/**
 * store main data
 */
define('DATA_DIR', realpath(__DIR__.'./../data/'));
define('CACHE_DIR', DATA_DIR.'/cache/');
define('POSTS_DIR', DATA_DIR.'/posts/');

// 加载配置类
include_once APP_DIR."./classes/Config.php";

// 全局的配置
$config = new Config(DATA_DIR.'/config.php');

// setup default config
{
    $config->addDefault('timezone','Asia/Chongqing','设置时区,在计算时间时才不会报错');

    $config->addDefault('blog_email','wenermail@gmail.com');
    $config->addDefault('blog_title','One world, one wener.');
    $config->addDefault('meta_description','One world, one wener.');

    $config->addDefault('language','zh-cn');

    $config->addDefault('active_template','default');

    $config->addDefault('posts_pre_page', 16);
    $config->addDefault('date_format', '%Y/%m/%d','Set the time format, see http://strftime.net/');

	$config->addDefault('intro_title', "Wener");
	$config->addDefault('intro_text', '');

	$config->addDefault('extra_links', array(
		'github'=> 'https://github.com/WenerLove/tellets',
		'Get Start' => 'https://github.com/WenerLove/tellets',
	));
}

// 使用配置来设置环境
{
    date_default_timezone_set($config['timezone']);
	ini_set('display_errors', true);

	define('BLOG_URL', $config['blog_url']);
	define('BLOG_TITLE', $config['blog_title']);
	define('INTRO_TITLE', $config['intro_title']);
	define('INTRO_TEXT', $config['intro_text']);

	define('TEMPLATE_DIR', DATA_DIR.'/templates/'.$config['active_template'].'/');
	define('TEMPLATE_URL', $config['blog_url'].'/data/templates/'.$config['active_template'].'/');

	// 似乎无效, 在 5.3 以上设置应该是可以用的
	// http://php.net/manual/zh/ini.core.php
	ini_set('short_open_tag', 1);

	// Get the current page.
	$currentpage  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] : 'https://'.$_SERVER["SERVER_NAME"];
	$currentpage .= $_SERVER["REQUEST_URI"];
	// page type
	define('IS_HOME', $currentpage == BLOG_URL);
	define('IS_CATEGORY', (bool)strstr($_SERVER['REQUEST_URI'], '/category/'));
	define('IS_TAG', (bool)strstr($_SERVER['REQUEST_URI'], '/tag/'));
	define('IS_SINGLE', !(IS_HOME || IS_CATEGORY || IS_TAG));
}


// 完成配置加载,加载使用类
foreach (glob(APP_DIR."./classes/*.php") as $filename)
{
	include_once $filename;
}


$postHelper = new FilePostHelper(CACHE_DIR.'/post.meta');
$dropplets = new Dropplets();
$dropplets->config = &$config;
$dropplets->postHelper = &$postHelper;


// load plugins
foreach (glob(APP_DIR."./plugins/*.php") as $filename)
{
	include $filename;
}