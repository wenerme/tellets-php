<?php
use FeedWriter\Atom;
use FeedWriter\RSS2;

Hook::AddHook(Hook::RESOLVE_REQUEST, 'rssOratomAction');

/**
 * @param Request $request
 */
function rssOratomAction($request)
{
	if(false == $request->isAction())
		return;

	global $config, $postHelper;
	$action = $request->getAction();


	if ($action == 'rss' || $action == 'atom')
	{
		($action=='rss') ? $feed = new RSS2() : $feed = new Atom();

		$feed->setTitle($config['blog_title']);
		$feed->setLink($config['blog_url']);
		$feed->setEncoding('utf-8');

		if($action=='rss') {
			$feed->setDescription($config['meta_description']);
			$feed->setChannelElement('language', $config['language']);
			$feed->setChannelElement('pubDate', date(DATE_RSS, time()));
		} else {
			$feed->setChannelElement('author', $config['blog_title'].' - ' . $config['author_email']);
			$feed->setChannelElement('updated', date(DATE_RSS, time()));
		}

		$posts = $postHelper->getPostList();

		if($posts)
		{
			$c=0;
			foreach($posts as $post)
			{
				if($c<$config['feed_max_items'])
				{
					$item = $feed->createNewItem();

					// Remove HTML from the RSS feed.
					$item->setTitle(substr($post['title'], 4, -6));
					$item->setLink(rtrim($config['blog_url'], '/').'/'.$post['link']);
					$item->setDate($post['date']);


					if($action=='rss') {
						$item->addElement('author', $post['title']);
						$item->addElement('guid', rtrim($config['blog_url'], '/').'/'.$post['link']);
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
}