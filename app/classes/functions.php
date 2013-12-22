<?php

/**
 * 将字符串转换为可以放在url里的标题
 * @param string $string
 */
function to_link_title($string)
{
	$result = strtolower($string);
	// 将多个空格转换为单个空格
	// 将空格转换为 '-' 更具阅读性

	$result = preg_replace('#\s+#','-',$result);

	// 移除保留的文件名字符 http://en.wikipedia.org/wiki/Filename#Number_of_names_per_file
	// \/?%*:|"<>.
	// 移除保留的URL字符 http://en.wikipedia.org/wiki/Percent-encoding#Types%5Fof%5FURI%5Fcharacters
	// !*'();:@&=+$,/?#[]

	// 即: \/?%*:|"<>.!'();@&=+$,#[]
	// 部分被替换为 '-' 以分割
	$result = preg_replace('#[&|:]#','-',$result);
	// 移除
	$result = preg_replace('#[\\\/?%*:|"<>.!\'();@&=+$,\#\[\]]#','',$result);

	return $result;
}

/**
 * @param Post $post
 * @throws InvalidArgumentException
 */
function get_post_cache_file_name($post)
{
	if(!$post){throw new InvalidArgumentException('$post 参数无效.');}
	$fn = '';
	$fn .= sprintf('-%s.post',$post->getMeta('hash'));

	return $fn;
}

function get_header()
{
	Hook::TriggerEvent(Hook::GENERATE_HEADER,array());
}

function get_footer()
{
	Hook::TriggerEvent(Hook::GENERATE_FOOTER,array());
}

function get_tag_link($item)
{
	return sprintf('%s/tag/%s',rtrim(BLOG_URL,'/'),$item);
}
function get_post_link($item)
{
	return sprintf('%s/%s',rtrim(BLOG_URL,'/'),$item['link']);
}
function get_category_link($item)
{
	return sprintf('%s/category/%s',rtrim(BLOG_URL,'/'),$item);
}

/**
 * 获取该父类的所有子类
 *
 * @param $parentClassName
 * @return array
 */
function get_subclasses($parentClassName)
{
	$classes = array();
	foreach (get_declared_classes() as $className)
	{
		if (is_subclass_of($className, $parentClassName))
			$classes[] = $className;
	}
	return $classes;
}

/**
 * see this http://en.wikipedia.org/wiki/Byte_order_mark
 * @param string $str
 */
function remove_byte_order_mark($str)
{
	// UTF-8	EF BB BF
	// UTF-16 (BE)	FE FF
	// UTF-16 (LE)	FF FE
	// UTF-32 (BE)	00 00 FE FF
	// UTF-32 (LE)	FF FE 00 00
	// GB-18030[t 1]	84 31 95 33
	return preg_replace('~^(?:
	(?:\xEF\xBB\xBF) # UTF8
	|(?:\xFE\xFE) # UTF-16 (BE)
	|(?:\xFF\xFE) # UTF-16 (LE)
	)~x','',$str);
}

/**
 * 返回当前页面的 URL
 * @return string
 */
function get_current_url()
{
	// 使用的原来的计算方法
	// 考虑使用这个版本,但是计算的多些 http://stackoverflow.com/questions/6768793

	// Get the components of the current url.
	$protocol = @( $_SERVER["HTTPS"] != 'on') ? 'http://' : 'https://';
	$domain = $_SERVER["SERVER_NAME"];
	$port = $_SERVER["SERVER_PORT"];
	$path = $_SERVER["REQUEST_URI"];

	$url = '';
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
	return $url;
}

/**
 * 返回应用的url
 * @return string
 */
function get_app_url()
{
	// 因为所有的操作会跳转到 index.php,所以这一步会成功
	$dir = dirname($_SERVER['SCRIPT_NAME']);
	$url = get_current_url();
	$start = strpos($url, $dir) + strlen($dir);
	$url = substr($url, 0, $start);
	return $url;
}