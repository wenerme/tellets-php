<article class="post">
	<header class="post-header">
		<h1><a href="<?=get_post_link($post)?>"><?=$post['title']?></a></h1>
		<aside class="post-details">
			<ul>
				<li><?=strftime($config['date_format'], $post['date']) ?></li>
			</ul>
		</aside>
	</header>
	<!-- #end header -->
	<div class="post-content <?=$request->isSingle()?'':'post-intro'?>">
		<?php
			// 判断是在单页文章还是在文章列表页面,如果在列表页面就只显示摘要
			// 如果没有摘要则直接显示内容
			echo $request->isSingle() || !isset($post['intro'])? $post->getContent(): $post['intro'];
		?></div>
	<!-- #end intro -->
	<div class="post-meta">

		<?php if(isset($post['category'])):?>
			<span class="category-container">Category:
				<?php
				/*
				$categories = is_array($post['category'])?$post['category']: array($post['category']);
				array_walk($categories,function(&$item){
					printf('<a class="category" href="%s">%s</a>',getCategoryLink($item),$item);
				});*/
				printf('<a class="category" href="%s">%s</a>',get_category_link($post['category']),$post['category']);
				?>
		</span>
		<?php endif;?>
		<!-- end category -->

		<?php if(isset($post['tag']) && count($post['tag']) > 0):?>
		<span class="tag-container">Tags:
			<?php
				array_walk($post['tag'],function(&$item){
					printf('<a class="tag"  href="%s">%s</a>',get_tag_link($item),$item);
				});
			?>
		</span>
		<?php endif;?>
		<!-- end tags -->

		<?php if($request->isPages() && isset($post['intro'])): //在列表页面,并且有摘要时才显示 read more ?>
		<a href="<?=get_post_link($post)?>" class="read-more">READ MORE</a>
		<?php endif;?>

	</div>
</article>
<!-- #end <?=$post['link']?> -->