<@ ../snippets/title.php @>
<@ if not @{ +main } and @{ textTeaser | def (@{ text }) } @>
	@{ textTeaser | markdown }
	@{ text | markdown }
<@ else @>
	@{ +main }
<@ end @>