<?php

class HTMLParser extends MarkdownParser
{
	const EXTENSION = '#\.html?$#i';
	public function parseContentOnly($content)
	{
		return $content;
	}
} 