<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<nav class="baker-navbar">
		<ul class="baker-navbar-nav" data-uk-sticky="{top: 0, media: '(min-height: 450px)'}">	
			<li class="baker-navbar-brand<@ if @{ :template | match('/sidebar/') } @> uk-hidden-large<@ end @>">
				<@ logo.php @>
			</li>
			<li class="uk-hidden-small">
				<div class="baker-navbar-items">
					<# 
					Expose variable to dashboard
					@{ checkboxShowInNavbar } 
					#>
					<@ newPagelist { 
						excludeHidden: false,
						match: '{ "checkboxShowInNavbar": "/[^0]+/" }'
					} ~@>
					<@ foreach in pagelist ~@>
						<a href="@{ url }" class="baker-navbar-items-button">
							@{ title }
						</a>	
					<@~ end @>
				</div>
			</li>
			<!-- <li class="baker-navbar-search">
				<@ search.php @>
			</li> -->
			<li class="uk-visible-large">
				<div class="baker-navbar-icons uk-flex">
					<@ if @{ urlGithub } @>
						<a
						href="@{ urlGithub }"
						title="GitHub"
						target="_blank"
						>
							<i class="uk-icon-github-alt"></i>
						</a>
					<@ end @>
					<@ if @{ urlTwitter } @>
						<a
						href="@{ urlTwitter }"
						title="Twitter"
						target="_blank"
						>
							<i class="uk-icon-twitter"></i>
						</a>
					<@ end @>
					<@ if @{ urlFacebook } @>
						<a
						href="@{ urlFacebook }"
						title="Facebook"
						target="_blank"
						>
							<i class="uk-icon-facebook-square"></i>
						</a>
					<@ end @>
					<@ if @{ urlInstagram } @>
						<a
						href="@{ urlInstagram }"
						title="Instagram"
						target="_blank"
						>
							<i class="uk-icon-instagram"></i>
						</a>
					<@ end @>
				</div>
			</li>
			<li 
			<@ if @{ :template | match('/sidebar/') } @>
				class="uk-hidden-large"	
			<@ end @>  
			>
				<a 
				href="#baker-sidebar-modal"
				class="baker-navbar-toggle" 
				data-uk-modal
				>
					<span aria-hidden="true"></span>
					<span aria-hidden="true"></span>
					<span aria-hidden="true"></span>
				</a>
			</li>
		</ul>
	</nav>	
	<@ sidebar_modal.php @>
	