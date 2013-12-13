<article class="post">
	<header class="post-header">
		<h1><a href="<?=getPostLink($post)?>"><?=$post['title']?></a></h1>
		<aside class="post-details">
			<ul>
				<li><?=strftime($config['date_format'], $post['date']) ?></li>
			</ul>
		</aside>
	</header>
	<!-- #end header -->
	<div class="post-content <?=IS_SINGLE?'':'post-intro'?>">
		<?php
			// 判断是在单页文章还是在文章列表页面,如果在列表页面就只显示摘要
			// 如果没有摘要则直接显示内容
			echo IS_SINGLE || !isset($post['intro'])? $post->getContent(): $post['intro'];
		?></div>
	<!-- #end intro -->
	<div class="post-meta">

		<?php if(isset($post['category']) && count($post['category']) > 0):?>
			<span class="category-container">Categories:
				<?php
				array_walk($post['category'],function(&$item){
					printf('<a class="category" href="%s">%s</a>',getCategoryLink($item),$item);
				});
				?>
		</span>
		<?php endif;?>
		<!-- end categories -->

		<?php if(isset($post['tag']) && count($post['tag']) > 0):?>
		<span class="tag-container">Tags:
			<?php
				array_walk($post['tag'],function(&$item){
					printf('<a class="tag"  href="%s">%s</a>',getTagLink($item),$item);
				});
			?>
		</span>
		<?php endif;?>
		<!-- end tags -->

		<?php if($request->isPages() && isset($post['intro'])): //在列表页面,并且有摘要时才显示 read more ?>
		<a href="<?=getPostLink($post)?>" class="read-more">READ MORE</a>
		<?php endif;?>

	</div>
</article>
<!-- #end <?=$post['link']?> -->