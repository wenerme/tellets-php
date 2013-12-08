<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">

	<title><?=$post['title']?> - <?=BLOG_TITLE?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href='//cdnjs.cloudflare.com/ajax/libs/normalize/2.1.3/normalize.min.css' rel='stylesheet'>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<link href='<?=TEMPLATE_URL ?>/styles/style.css' rel='stylesheet'>
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
			if(is_array($config['extra_links']))
				foreach($config['extra_links'] as $text => $url)
					echo "<a href='$url'>$text</a>";
			?>
		</nav>
	</div>
</header>
<!-- #end-header -->