<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<!DOCTYPE html>
<html lang="en" class="baker-@{ :template | sanitize }">
<head>
	<@ set {
		<# 
		The :version var is used globally for cache busting also 
		in other snippets and templates! 
		#>
		:version: '<?php 
			$Cache = new Automad\Core\Cache();
			echo $Cache->getSiteMTime();
		?>',
		<# Variables used by the MetaTag extension. #>
		:tagTitle: @{ metaTitle | def("@{ sitename } / @{ title | def('404') }") },
		:tagDescription: @{ metaDescription | def(@{ +main }) | stripTags }
	} @>
	<title>@{ :tagTitle }</title>
	<@ Automad/MetaTags {
		description: @{ :tagDescription },
		ogTitle: @{ :tagTitle },
		ogDescription: @{ :tagDescription },
		ogImage: @{ ogImage },
		twitterCard: 'summary_large_image'
	} @>
	<@ favicons.php @>
	<link href="https://fonts.googleapis.com/css?family=Fira+Mono:400,500" rel="stylesheet">
	<link href="/packages/@{ theme }/dist/libs.min.css?v=@{ :version }" rel="stylesheet">
	<link href="/packages/@{ theme }/dist/baker.min.css?v=@{ :version }" rel="stylesheet">
	<script src="/packages/@{ theme }/dist/libs.min.js?v=@{ :version }"></script>
	<script src="/packages/@{ theme }/dist/baker.min.js?v=@{ :version }"></script>
	<script>
		var autocomplete = <@ autocomplete.php @>;
	</script>
	@{ itemsHeader }
	<style>
		@media (max-width: 52.5rem) {
			.baker-nav a, .baker-nav a:hover{
				background-color:#f6f6f600!important;
				
			}
			.baker-nav a:hover{
				
				font-weight:bold;
			}
			.uk-active {
	color: #121212 !important;
	text-transform: uppercase;
	font-weight: bold;
}
    .baker-navbar-toggle span {
       
        left: auto!important;
    
        right: 0.5rem!important;
    }

	.cards {
		grid-template-columns: 1fr;
       
	}

    .am-gallery-masonry{
        margin-bottom: auto!important;
        grid-auto-rows: auto;
    }
    .am-gallery-img-small {
        display: inline-block;
    }

    .am-gallery-masonry-item {
        
        padding-bottom: calc(var(--am-gallery-gap)/3);
        
    }


	.baker-navbar-nav {
	transition: border-color 0.2s ease, padding 0.2s ease!important;
	}
	.baker-navbar-nav.uk-sticky-init.uk-active {
        padding-top: 6px!important;
    }
}
	</style>
</head>

<body>
	<div class="uk-container uk-container-center uk-position-relative">
