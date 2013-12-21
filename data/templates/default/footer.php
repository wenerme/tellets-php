<footer id="footer" class="container">
	<?php
	$tags = $postHelper->getTagList();
	$categories = $postHelper->getCategoryList();
	?>

	<ul class="footer-column">
		<li class="main-column">
			<div>wener is good</div>
			<div>desc</div>
			<div>sub</div>
			<div>copyrigt</div>
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