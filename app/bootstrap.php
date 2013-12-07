<?php
// setup all dir
define('APP_DIR', __DIR__);
define('DATA_DIR', realpath(__DIR__.'./../data/'));
define('CACHE_DIR', DATA_DIR.'/cache/');
define('POSTS_DIR', DATA_DIR.'/posts/');
define('TEMPLATE_DIR', DATA_DIR.'/templates/');

// load classes
foreach (glob(__DIR__."./classes/*.php") as $filename)
{
    include_once $filename;
}

// 定义全局使用的变量
$config = new Config(DATA_DIR.'/config.php');

// setup default config
{
    $config->addDefault('timezone','Asia/Chongqing','设置时区,在计算时间时才不会报错');

    $config->addDefault('blog_email','wenermail@gmail.com');
    $config->addDefault('blog_title','One world, one wener.');
    $config->addDefault('meta_description','One world, one wener.');
    $config->addDefault('intro_text','This is my blog.');

    $config->addDefault('language','zh-cn');

    $config->addDefault('active_template','default');

    $config->addDefault('posts_pre_page', 16);
    $config->addDefault('date_format', 'YYYY/mm/dd');
}


// use config setup env
{
    date_default_timezone_set($config['timezone']);
}

$dropplets = new Dropplets();

// load plugins
foreach (glob(__DIR__."./plugins/*.php") as $filename)
{
	include $filename;
}