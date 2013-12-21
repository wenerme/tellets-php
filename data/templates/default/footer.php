
<footer id="footer">
	<?php
		$tags = $postHelper->getTagList();
	?>
	Tags:
	<ul>
		<?php
		foreach($tags as $tag =>$num):
		?>
		<li><?=$tag."($num)"?></li>
		<?php
		endforeach;
		?>
	</ul>
</footer>
<!-- #end-header -->
</body>
</html>