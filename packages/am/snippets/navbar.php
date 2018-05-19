<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>

	<div class="navbar">
		<div class="uk-container uk-container-center">
			<nav class="uk-navbar">
				<a href="/" class="uk-navbar-brand"><@ 
					with @{ logo | def('/shared/*logo*') } { height: 50 } 
						@><img src="@{ :fileResized }" alt="@{ :basename }"><@ 
					else 
						@>@{ sitename }<@ 
					end 
				@></a>
			    <div class="uk-navbar-flip">
			        <ul class="uk-navbar-nav uk-visible-large">
						<@ newPagelist { excludeHidden: false } @>
						<@ foreach in pagelist @>
							<@ if @{ checkboxShowInNavbar } @>
							<li<@ if @{ :current } @> class="uk-active"<@ end @>>
								<a href="@{ url }">@{ title }</a>
							</li>
							<@ end @>
						<@ end @>				
			        </ul>
					<a 
					href="#modal-nav"
					class="navbar-toggle uk-navbar-content" 
					data-modal-toggle="#modal-nav"
					>
						<span aria-hidden="true"></span>
						<span aria-hidden="true"></span>
						<span aria-hidden="true"></span>
					</a>
			    </div>
			</nav>

		</div>
	</div>

	<# Modal with site tree and search. #>
	<div id="modal-nav" class="uk-modal">		
		<div class="uk-modal-dialog uk-modal-dialog-blank">
			<div class="uk-container uk-container-center navbar-push">
				<# Search. #>
				<@ if @{ urlSearchResults } @>
				<form class="uk-form uk-width-1-1 uk-width-medium-1-2" action="@{ urlSearchResults }" method="get">
					<script>
						var autocomplete = <@ autocomplete.php @>
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