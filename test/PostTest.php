<?php
require_once '../app/bootstrap.php';

class ConfigTest extends PHPUnit_Framework_TestCase
{
	static $simple_post = <<<'EOT'
		
<!-- title : this is a simple post -->
<!-- author: wener -->

<!-- category: post ,cat -->
<!-- tag: story, first -->

<!-- date: 2013-12-1 -->

<!-- key: value -->
				
	<!-- will  strip     these   space : value -->			
simple post, simple intro.
		<!-- more -->
			begin
simpel content.
	end
EOT;


	public function test_parse()
	{
		
		$post = Post::ParseContent(self::$simple_post);
		
		var_dump($post);
		
		$this->assertEquals('this is a simple post', $post->getTitle());
		$this->assertEquals('wener', $post->getAuthor());
		$this->assertEquals(array('post','cat'),$post->getCategory());
		$this->assertEquals(array('story','first'),$post->getTag());
		$this->assertEquals(mktime(0,0,0,12,1,2013), $post->getDate());
		
		
		$this->assertEquals('value', $post->getMeta('key'));
		$this->assertEquals('value', $post->getMeta('will strip these space'));
		
	}
	
	public function test_config_op()
	{
		
	}
}