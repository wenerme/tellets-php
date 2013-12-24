<?php
use FeedWriter\Atom;
use FeedWriter\RSS2;

Hook::AddHook(Hook::RESOLVE_REQUEST, 'rssOratomAction');
Hook::AddHook(Hook::RESOLVE_POST, 'basic_post_resolver');
Hook::AddHook(Hook::FIND_POST_LIST, 'posts_in_post_dir');
Hook::AddHook(Hook::RESOLVE_REQUEST, 'update_action');
Hook::AddHook(Hook::GENERATE_HEADER, 'generate_default_header');


/**
 * @param Request $request
 */
function rssOratomAction($request)
{
	if (false == $request->isAction())
		return;
	$action = $request->getAction();
	if (false == ($action == 'rss' || $action == 'atom'))
		return;

	global $config, $postHelper;


	($action == 'rss') ? $feed = new RSS2() : $feed = new Atom();

	$feed->setTitle(BLOG_TITLE);
	$feed->setLink(BLOG_URL);
	$feed->setEncoding('utf-8');

	if ($action == 'rss')
	{
		$feed->setDescription($config['meta_description']);
		$feed->setChannelElement('language', $config['language']);
		$feed->setChannelElement('pubDate', date(DATE_RSS, time()));
	} else
	{
		$feed->setChannelElement('author', BLOG_TITLE . ' - ' . $config['author_email']);
		$feed->setChannelElement('updated', date(DATE_RSS, time()));
	}

	$posts = $postHelper->getPostList();

	if ($posts)
	{
		$c = 0;
		foreach ($posts as $post)
		{
			if ($c < $config['feed_max_items'])
			{
				/**
				 * @params Item
				 */
				$item = $feed->createNewItem();

				// Remove HTML from the RSS feed.
				$item->setTitle($post['title']);
				$item->setLink( get_post_link($post));
				$item->setDate($post['date']);
				$item->setId($post['link']);

				if ($action == 'rss')
				{
					$item->addElement('author', $post['title']);
					$item->addElement('guid', get_post_link($post));
				}


				$item->setDescription($post->getIntroOrContent());

				$feed->addItem($item);
				$c++;
			}
		}
	}
	$feed->printFeed();
	exit();
}

function basic_post_resolver(&$post, $name)
{
	if ($post)
		return;

	global $postHelper;
	$list = $postHelper->getPostList();

	foreach ($list as $p)
		if ($name == $p['link'] || $name == $p['hash'])
		{
			$post = $p;
			break;
		}

}

function posts_in_post_dir(&$list)
{
	// load plugins
	foreach (glob(POSTS_DIR . '/*') as $filename)
	{
		$list[] = $filename;
	}
}

/**
 * @param Request $request
 */
function update_action($request)
{
	if (!($request->isAction() && $request->getAction() === 'update'))
		return;

	set_time_limit (0);
	global $tellets;
	$tellets->Update();
}

function generate_default_header()
{
	$K = 'strval';

	echo <<<EOT
<meta name="generator" content="Tellets {$K(TELLETS_VERSION)}">
EOT;

}