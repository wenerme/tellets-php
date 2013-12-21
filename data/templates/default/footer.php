
<footer id="footer">
	<?php
		$tags = $postHelper->getTagList();
		$categories = $postHelper->getCategoryList();
	?>
	Tags:
	<ul>
		<?php
		foreach($tags as $item =>$num):
		?>
		<li><?=$item."($num)"?></li>
		<?php
		endforeach;
		?>
	</ul>

	Category:
	<ul>
		<?php
		foreach($categories as $item =>$num):
			?>
			<li><?=$item."($num)"?></li>
		<?php
		endforeach;
		?>
	</ul>
</footer>
<!-- #end-header -->
</body>
</html>