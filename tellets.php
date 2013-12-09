<?php
session_start();

require_once __DIR__.'./app/bootstrap.php';

include_once APP_DIR.'./library/Feed.php';
include_once APP_DIR.'./library/Item.php';
include_once APP_DIR.'./library/RSS2.php';
include_once APP_DIR.'./library/ATOM.php';

use FeedWriter\Atom;
use FeedWriter\RSS2;

if(false === isset($config['password']))
    goto FIRST_RUN;

//var_dump($dropplets->getPostFileList());
//var_dump($_SERVER,$_GET);

//$dropplets->Update();

$filename = null;
$posts = $post = null;
isset($_GET['filename']) && $filename = $_GET['filename'];

if ($filename == 'rss' || $filename == 'atom')
{
	($filename=='rss') ? $feed = new RSS2() : $feed = new Atom();

	$feed->setTitle($config['blog_title']);
	$feed->setLink($config['blog_url']);
	$feed->setEncoding('utf-8');

	if($filename=='rss') {
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


				if($filename=='rss') {
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

if(IS_HOME)
{
	//显示主页
	$posts = $postHelper->getPostList();
}

preg_match('#(?<type>category|tag)\/(?<value>[^\#&]+)#i',$filename,$matches);
if(!$posts && isset($matches['value']))
{
	if(IS_TAG)
		//显示 tag 文章列表
		$posts = $postHelper->getPostListOfTag($matches['value']);
	else
		// 显示 分类文章列表
		$posts = $postHelper->getPostListOfCategory($matches['value']);
}

if($filename)
{
	// 显示单个文章
	$post = $dropplets->resolvePost($filename);
}

// display
if($posts)
{
	if(IS_HOME)
		include TEMPLATE_DIR.'/index.php';
	else
		include TEMPLATE_DIR.'/posts.php';
}elseif($post)
{
	include TEMPLATE_DIR.'/post.php';
}else{
	include TEMPLATE_DIR.'/404.php';
}
exit();

FIRST_RUN:
// 第一粗运行,设置密码
if(isset($_POST['password']))
{
    $dropplets->SetUp();
    // Redirect
    header("Location: " . $config['blog_url']);
    exit;
}

// Get the components of the current url.
$protocol = @( $_SERVER["HTTPS"] != 'on') ? 'http://' : 'https://';
$domain = $_SERVER["SERVER_NAME"];
$port = $_SERVER["SERVER_PORT"];
$path = $_SERVER["REQUEST_URI"];

// Check if running on alternate port.
if ($protocol === "https://") {
    if ($port == 443)
        $url = $protocol . $domain;
    else
        $url = $protocol . $domain . ":" . $port;
} elseif ($protocol === "http://") {
    if ($port == 80)
        $url = $protocol . $domain;
    else
        $url = $protocol . $domain . ":" . $port;
}

$url .= $path;

// setup config
$config['blog_url'] = $url;

// Check if the install directory is writable.
$is_writable = (TRUE == is_writable(dirname(__FILE__) . '/'));
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Let's Get Started</title>
    <link rel="stylesheet" href="./data/assets/style.css" />
    <link href='http://fonts.googleapis.com/css?family=Lato:100,300' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400' rel='stylesheet' type='text/css'>
    <link rel="shortcut icon" href="./data/assets/images/favicon.png">
</head>

<body class="dp-install">
<form method="POST" action="./tellets.php">
    <a class="dp-icon-dropplets" href="http://dropplets.com" target="_blank"></a>

    <h2>Install Dropplets</h2>
    <p>Welcome to an easier way to blog.</p>

    <input type="password" name="password" id="password" required placeholder="Choose Your Password">
    <input type="password" name="password-confirmation" id="password-confirmation" required placeholder="Confirm Your Password" onblur="confirmPass()">

    <input hidden type="text" name="blog_email" id="blog_email" value="wenermail@gmail.com">
    <input hidden type="text" name="blog_url" id="blog_url" value="<?php echo($url) ?><?php if ($url == $domain) { ?>/<?php } ?>">
    <input hidden type="text" name="template" id="template" value="default">
    <input hidden type="text" name="blog_title" id="blog_title" value="Welcome to Tellets">
    <textarea hidden name="meta_description" id="meta_description"></textarea>
    <input hidden type="text" name="intro_title" id="intro_title" value="Welcome to Tellets">
    <textarea hidden name="intro_text" id="intro_text">In a flooded selection of overly complex solutions, Dropplets has been created in order to deliver a much needed alternative. There is something to be said about true simplicity in the design, development and management of a blog. By eliminating all of the unnecessary elements found in typical solutions, Dropplets can focus on pure design, typography and usability. Welcome to an easier way to blog.</textarea>

    <button type="submit" name="submit" value="submit">k</button>
</form>

<?php if (!$is_writable) { ?>
    <p style="color:red;">It seems that your config folder is not writable, please add the necessary permissions.</p>
<?php } ?>

<script>
    function confirmPass() {
        var pass = document.getElementById("password").value
        var confPass = document.getElementById("password-confirmation").value
        if(pass != confPass) {
            alert('Your passwords do not match!');
        }
    }
</script>
</body>
</html>
