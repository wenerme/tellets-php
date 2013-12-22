

<?php include __DIR__.'/header.php'?>

<div id="main-wrap">
	<div id="content" class="container">
		<?php
			include __DIR__.'/post_part.php';
		?>
		<div class="pagination">
			<?php if($postHelper->hasPrevPost($post)):
				$thepost = $postHelper->getPrevPost($post);
				?>
			<a href="<?=getPostLink($thepost)?>" class="prev-page">&lt; <?=$thepost['title']?></a>
			<?php endif;?>

			<?php if($postHelper->hasNextPost($post)):
				$thepost = $postHelper->getNextPost($post);
				?>
				<a href="<?=getPostLink($thepost)?>" class="next-page"><?=$thepost['title']?> &gt;</a>
			<?php endif;?>
		</div>
	</div>
	<!-- #end-content -->
</div>
<!-- #end-main-wrap -->

<?php include __DIR__.'/footer.php'?>