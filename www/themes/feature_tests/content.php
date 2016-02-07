{@ elements/header.php @}

	<div class="row">
		<div class="col-md-12">
			<h1>{[ title ]}</h1>
			<hr>
			<h2>String Function Tests</h2>
			<hr>
			<h4>Raw content (no string function)</h4>
			{[ test ]}
			<hr>
			<h4>Sanitized</h4>
			{[ test | sanitize ]}
			<hr>
			<h4>Sanitized without dots</h4>
			{[ test | sanitize (true) ]}
			<hr>
			<h4>Shortened to max 30 chars and sanitized without dots</h4>
			{[ test | 30 | sanitize (true) ]}
			<hr>
			<h4>Stripped Tags and shotened to 70 chars</h4>
			{[ test | stripTags | 70 ]}
			<hr>
			<h4>Stripped Tags and shotened to 70 chars with a replacement for the standard ellipsis</h4>
			{[ test | stripTags | shorten (70, "<br><br>Read more ...") ]}
			<hr>
			<h4>Stripped Tags, shortened and uppercased</h4>
			{[ test | stripTags | 70 | strtoupper ]}
			<hr>
			<h4>String lenght</h4>
			{[ test | strlen ]}
			<hr>
			<h4>Markdown</h4>
			{[ test | markdown ]}
		</div>
	</div>
	{@ pagelist { type: "children", parent: "/example-pages" } @}
	{@ foreach in pagelist @}
	<hr>
	<div class="row">
		<div class="col-md-4">
			<h2>{[ title | stripTags ]}</h2>
			<h3>{[ subtitle | stripTags ]}</h3>
		</div>
		<div class="col-md-8">
			<br>
			{@ img { file: "*.jpg", width: 800, class: "img-responsive" } @}
			<br>
			{[ text | shorten (300, "") ]}
			<br><br>
			<a class="btn btn-default" href="{[ url ]}">Read more ...</a>
			<br><br>
		</div>	
	</div>
	{@ end @}
	
{@ elements/footer.php @}