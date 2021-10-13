<!DOCTYPE html>
<html lang="en" class="@{ theme | sanitize }">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@{ metaTitle | def('@{ sitename } / @{ title | def ("404") }') }</title>
	<@ elements/metatags.php @>
	<@ elements/favicons.php @>
	<# 
	
	To make sure the following variables are always available in the dashboard, 
	they can be included in a comment block.
	
	@{ imageCard } 
	@{ checkboxHideThumbnails }
	
	#>
	<link href="/packages/standard/dist/standard.min.css" rel="stylesheet">
	<script src="/packages/standard/dist/standard.min.js"></script>
	<@ elements/colors_header.php @>
	<# Add optional header items. #>
	@{ itemsHeader }
</head>

<body class="@{ :template | sanitize }">
	<@ elements/navbar.php @>
	@{ +hero | replace ('/^(.+)$/is', '<section class="hero content">$1</section>') }
	<div class="uk-container uk-container-center">
		<# 

		Define the default main snippet to display the actual content. 
		The snippet can be overriden before including the actual template in order to extend a template.

		#>
		<@ snippet main @>
			<@ elements/header.php @>
				<main class="content uk-block">
					<@ elements/content.php @>
					<@ elements/prev_next.php @>
				</main>
				<div class="content uk-block">
					<@ elements/related_posts.php @>
				</div>
			<@ elements/footer.php @>
		<@ end @>
		<# 

		Get the output of the main snippet. 

		#>
		<@ main @>
		<footer class="uk-block">
			<div class="am-block footer uk-margin-bottom">
				<ul class="uk-grid uk-grid-width-medium-1-2" data-uk-grid-margin>
					<li>
						<# @{ checkboxShowInFooter } #>
						<@~ newPagelist { 
							excludeHidden: false,
							match: '{ "checkboxShowInFooter": "/[^0]+/" }' 
						} @>
						<@~ foreach in pagelist @>
							<a href="@{ url }"><@ elements/icon_title.php @></a><br />
						<@~ end @>
					</li>
					<li class="uk-text-right uk-text-left-small">
						<a href="/">
							&copy; @{ :now | dateFormat('Y') } @{ sitename }
						</a>
					</li>
				</ul>
				<# 

				Add optional footer items. 

				#>
				@{ itemsFooter }
			</div>
		</footer>
	</div>
</body>
</html>