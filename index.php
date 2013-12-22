<?php
session_start();

require_once __DIR__.'/app/bootstrap.php';

if(false === isset($config['password']))
    goto FIRST_RUN;

//$tellets->Update();

$request = new Request(@$_GET['params']);
Hook::TriggerEvent(Hook::RESOLVE_REQUEST, array($request));


function renderContext()
{
	global $request, $config, $postHelper;

	$post = $request->getSinglePost();
	$posts = $request->getPosts();

	// display
	if($posts)
	{
		if($request->isHome())
			include TEMPLATE_DIR.'/index.php';
		else
			include TEMPLATE_DIR.'/posts.php';
	}elseif($post)
	{
		include TEMPLATE_DIR.'/post.php';
	}else{
		include TEMPLATE_DIR.'/message.php';
	}
}

renderContext();

exit();

FIRST_RUN:

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

if(strpos($path,'index.php'))
	$path = dirname($path);

$url .= $path;

// setup config
$config['blog_url'] = $url;

// 第一次运行,设置密码
if(isset($_POST['password']))
{
	$tellets->SetUp();
	// Redirect
	header("Location: " . $config['blog_url']);
	exit;
}

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
<form method="POST" action="index.php">
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
