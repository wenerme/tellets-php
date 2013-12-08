<?php

/**
 * 将字符串转换为可以放在url里的标题
 * @param $string
 */
function toLinkTitle($string)
{
	$result = $string;
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
	$result = preg_replace('#[\/?%*:|"<>.!\'();@&=+$,#\[\]]#','',$result);

	return $result;
}

/**
 * @param Post $post
 * @throws InvalidArgumentException
 */
function getPostCacheFileName($post)
{
	if(!$post){throw new InvalidArgumentException('$post 参数无效.');}
	$fn = '';
	$fn .= sprintf('-%s.post',$post->getMeta('hash'));

	return $fn;
}