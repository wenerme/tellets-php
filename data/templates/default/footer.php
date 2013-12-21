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
			<div><?=$config['author']?> &copy; <?=strftime("%Y")?></div>
		</li>
		<li>
			<ul>
				<li>Tags</li>
				<?php foreach ($tags as $item => $num):?>
					<li><a href="<?=getTagLink($item) ?>">
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
					<li><a href="<?=getCategoryLink($item) ?>">
							<?= $item?>
							<small>(<?=$num?>)</small>
						</a>
					</li>
				<?php endforeach;?>
			</ul>
		</li>
	</ul>
</footer>
<!-- #end-header -->
</body>
</html>