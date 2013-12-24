<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">

	<title><?=$request->isSingle()?$post['title']. ' - ':''?><?=BLOG_TITLE?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<link href='<?=TEMPLATE_URL ?>/styles/normalize.css' rel='stylesheet'>
	<link href='<?=TEMPLATE_URL ?>/styles/style.css' rel='stylesheet'>
	<link href='<?=TEMPLATE_URL ?>/styles/markdown.css' rel='stylesheet'>

	<?php get_header();?>
</head>

<body>

<header id="header">
	<div id="header-container">
		<h1>
			<a href="<?=BLOG_URL ?>"><?=INTRO_TITLE ?></a>
			<small><?=INTRO_TEXT ?></small>
		</h1>

		<nav id="site-nav">
			<?php
			if(is_array($config[Config::NS_TEMPLATE]['extra_links']))
				foreach($config[Config::NS_TEMPLATE]['extra_links'] as $text => $url)
					echo "<a href='$url'>$text</a>";
			?>
		</nav>
	</div>
</header>
<!-- #end-header -->