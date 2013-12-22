<?php include __DIR__.'/header.php'?>

	<div id="main-wrap">
		<div id="content" class="container">
			<?php
			foreach($request->getPosts() as $post):
				include __DIR__.'/post_part.php';
			endforeach;?>
			<div class="pagination">
				<?php if($request->hasPrevPage()): ?>
				<a href="<?=$request->getPrevPageURL()?>" class="prev-page">&lt; Newer Posts</a>
				<?php endif?>
				<?php if($request->hasNextPage()): ?>
					<a href="<?=$request->getNextPageURL()?>" class="next-page">Older Posts &gt;</a>
				<?php endif?>
			</div>
		</div>
		<!-- #end-content -->
	</div>
	<!-- #end-main-wrap -->

<?php include __DIR__.'/footer.php'?>