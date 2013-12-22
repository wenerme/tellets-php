<footer id="footer" class="container">
	<?php
	$tags = $postHelper->getTagList();
	$categories = $postHelper->getCategoryList();
	?>

	<ul class="footer-column">
		<li class="main-column">
			<h2><a href="<?=BLOG_URL?>"><?=$config['intro_title']?></a></h2>
			<div><?=$config['intro_text']?></div>
			<hr/>
			<div>feed</div>
			<div>&copy; <?=strftime("%Y")?> <?=$config['author']?></div>
			<div>
				Powered by <a href="https://github.com/WenerLove/tellets">tellets</a>
				theme by <a href="http://blog.wener.me">wener</a>
			</div>
		</li>
		<li>
			<ul>
				<li>Tags</li>
				<?php foreach ($tags as $item => $num):?>
					<li><a href="<?=get_tag_link($item) ?>">
							<?= $item?>
							<small>(<?=$num?>)</small>
						</a>
					</li>
				<?php endforeach;?>
			</ul>
		</li>

		<li>
			<ul>
				<li>Categories</li>
				<?php foreach ($categories as $item => $num):?>
					<li><a href="<?=get_category_link($item) ?>">
							<?= $item?>
							<small>(<?=$num?>)</small>
						</a>
					</li>
				<?php endforeach;?>
			</ul>
		</li>
	</ul>
</footer>

<div class="container">
	<?php if($request->isSingle())// 只有在单页才显示
		switch($config[Config::NS_TEMPLATE]['comment_type']):
	case 'duoshuo':?>
	<!-- Duoshuo Comment BEGIN -->
	<div class="ds-thread"
	     data-thread-key="<?=$post['link']?>"
	     data-title="<?=$post['title']?>"
		>
	     </div>
	<script type="text/javascript">
		var duoshuoQuery = {short_name:"<?=$config[Config::NS_TEMPLATE]['comment_user']?>"};
		(function() {
			var ds = document.createElement('script');
			ds.type = 'text/javascript';ds.async = true;
			ds.src = 'http://static.duoshuo.com/embed.js';
			ds.charset = 'UTF-8';
			(document.getElementsByTagName('head')[0]
				|| document.getElementsByTagName('body')[0]).appendChild(ds);
		})();
	</script>
	<!-- Duoshuo Comment END -->

	<?php break;?>
	<?php case 'disqus':?>
		<div id="disqus_thread"></div>
		<script type="text/javascript">
			/* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
			var disqus_shortname = '<?=$config[Config::NS_TEMPLATE]['comment_user']?>'; // required: replace example with your forum shortname
			var disqus_identifier = '<?=$post['link']?>';
			var disqus_title = '<?=$post['title']?>';

			/* * * DON'T EDIT BELOW THIS LINE * * */
			(function() {
				var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
				dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
				(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
			})();
		</script>
	<?php break;?>
	<?php endswitch;// ($config[Config::NS_TEMPLATES]['comment_type']):?>
</div>
<!-- #end-comment -->

</body>
</html>