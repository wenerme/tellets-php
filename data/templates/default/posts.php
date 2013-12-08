<?php include __DIR__.'/header.php'?>

	<div id="main-wrap">
		<div id="content">
			<?php
			foreach($posts as $post):
				include TEMPLATE_DIR.'./post_part.php';
			endforeach;?>
			<div class="pagination">
				<a href="#" class="prev-page">&lt; Newer Posts</a>
				<a href="#" class="next-page">Older Posts &gt;</a>
			</div>
		</div>
		<!-- #end-content -->
	</div>
	<!-- #end-main-wrap -->

<?php include __DIR__.'/footer.php'?>