<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<# Navbar #>		
	<ul class="am-one-navbar uk-grid uk-margin-large-bottom">
		<# Logo or sitename. #>
		<li class="uk-width-2-4 uk-width-medium-1-4">
			<a href="/" class="am-one-brand">
				<@ with @{ logo | def('/shared/*logo*') } { height: @{ logoHeight | def(80) } } @>
					<img src="@{ :fileResized }" alt="@{ :basename }">
				<@ else @>
					@{ sitename }
				<@ end @>
			</a>
		</li>
		<# Menu with pages with checked "checkboxShowInNavbar". #>
		<li class="uk-width-2-4 uk-width-medium-3-4">
			<nav 
			class="uk-navbar" 
			data-uk-sticky="{top:25,showup:true,animation:'uk-animation-slide-top'}">
				<div class="uk-navbar-flip">
					<ul class="uk-navbar-nav">
						<@ newPagelist { excludeHidden: false } @>
						<@ foreach in pagelist @>
							<@ if @{ checkboxShowInNavbar } @>
							<li<@ if @{ :current } @> class="uk-active"<@ end @>>
								<a href="@{ url }" class="uk-visible-large">@{ title }</a>
							</li>
							<@ end @>
						<@ end @>
					</ul>
					<a 
					href="#am-one-modal-nav" 
					class="uk-navbar-toggle" 
					data-uk-modal
					></a>
				</div>
			</nav>
		</li>
	</ul>

	<# Modal with site tree and search. #>
	<div id="am-one-modal-nav" class="uk-modal">		
		<div class="uk-modal-dialog uk-modal-dialog-blank">
			<div class="uk-container uk-container-center">
				<a 
				href="#" 
				class="uk-margin-large-top uk-button uk-modal-close"
				>
					<i class="uk-icon-close"></i>&nbsp;
					Close
				</a>
				<# Search. #>
				<@ if @{ urlSearchResults } @>
				<form class="uk-form uk-width-1-1 uk-width-medium-1-2 uk-margin-large-top" action="@{ urlSearchResults }" method="get">
					<script>
						var autocomplete = <@ ../../elements/autocomplete.php @>
					</script>
					<div 
					class="uk-autocomplete uk-width-1-1" 
					data-uk-autocomplete='{source:autocomplete,minLength:2}'
					>
						<input 
						class="uk-form-controls uk-width-1-1" 
						type="text" 
						name="search" 
						placeholder="Search @{ sitename }" 
						required 
						/>	
					</div>
				</form>	
				<@ end @>
				<# Create snippet to be used recursively #>
				<@ snippet tree @>
					<# Only show children/siblings in current path #>
					<@ if @{ :currentPath } @>
						<# Only create new list in case the current context has children #>
						<@ if @{ :pagelistCount } @>
							<ul class="uk-nav uk-nav-side">
								<@ foreach in pagelist @>		
									<li<@ if @{ :current } @> class="uk-active"<@ end @>>
										<a href="@{ url }">@{ title }</a>
										<# Call tree snippet recursively #>
										<@ tree @>
									</li>	
								<@ end @>
							</ul>
						<@ end @>
					<@ end @>
				<@ end @>
				<# Create new pagelist including all children adapting to the current context. #>
				<@ newPagelist { type: 'children' } @>
				<div class="uk-margin-large-top uk-margin-large-bottom">
					<# Change context to the homepage #>
					<@ with "/" @>
						<ul class="uk-nav uk-nav-side">
							<li<@ if @{ :current } @> class="uk-active"<@ end @>>
								<a href="@{ url }">@{ title }</a>
							</li>
						</ul>
						<# Call recursive tree snippet #>
						<@ tree @>	
					<@ end @>
				</div>
			</div>
		</div>
	</div>