<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
	
<@ if @{ :template | match ('/sidebar/') } @>
	<@ set {
		:sidebarNav: true,
		:classNav: 'uk-hidden-large',
		:classSearch: 'uk-visible-large',
	} @>
<@ end @>

	<div class="navbar">
		<div class="uk-container uk-container-center">
			<nav class="uk-navbar">
				<a href="/" class="uk-navbar-brand">
					<@~ with @{ imageLogo } ~@>
						<img 
						src="<@ with @{ :file } { height: @{ logoHeight | def (40) } } @>@{ :fileResized }<@ end @>" 
						srcset="<@ with @{ :file } { height: @{ logoHeight | def (40) | *2 } } @>@{ :fileResized } 2x<@ end @>"
						alt="@{ :basename }"
						>
					<@~ else ~@>
						@{ brand | def (@{ sitename }) }
					<@~ end ~@>
				</a>
			    <div class="uk-navbar-flip">
			        <ul class="uk-navbar-nav uk-visible-large">
						<@ newPagelist { excludeHidden: false } @>
						<@~ foreach in pagelist ~@>
							<@ if @{ checkboxShowInNavbar } ~@>
								<li<@ if @{ :current } @> class="uk-active"<@ end @>>
									<a href="@{ url }">@{ title }</a>
								</li>
							<@~ end @>
						<@~ end ~@>				
					</ul>
					<@ if @{ :sidebarNav } and @{ urlSearchResults } @>
						<a 
						href="#modal-nav" 
						class="navbar-search-button @{ :classSearch }"
						data-modal-toggle="#modal-nav"
						>
							<svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 20 20">
								<path d="M8.188,2.047c3.386,0,6.141,2.754,6.141,6.141c0,3.386-2.755,6.141-6.141,6.141c-3.386,0-6.141-2.755-6.141-6.141 C2.047,4.801,4.801,2.047,8.188,2.047 M8.188,0C3.666,0,0,3.666,0,8.188c0,4.521,3.666,8.188,8.188,8.188 c4.521,0,8.188-3.666,8.188-8.188C16.375,3.666,12.709,0,8.188,0L8.188,0z"/>
								<path d="M18.75,20c-0.32,0-0.64-0.122-0.884-0.366l-5-5c-0.488-0.488-0.488-1.279,0-1.768s1.279-0.488,1.768,0l5,5 c0.488,0.488,0.488,1.279,0,1.768C19.39,19.878,19.07,20,18.75,20z"/>
							</svg>
						</a>
					<@ end @>
					<a 
					href="#modal-nav"
					class="navbar-toggle uk-navbar-content @{ :classNav }" 
					data-modal-toggle="#modal-nav"
					>
						<span aria-hidden="true"></span>
						<span aria-hidden="true"></span>
						<span aria-hidden="true"></span>
					</a>
					<# Modal with site tree and search. #>
					<div id="modal-nav" class="uk-modal">		
						<div class="uk-modal-dialog uk-modal-dialog-blank">
							<div class="uk-container uk-container-center">
								<# Search. #>
								<@~ if @{ urlSearchResults } @>
									<div class="uk-block uk-margin-bottom-remove uk-margin-top-remove">
										<form 
										class="uk-form" 
										action="@{ urlSearchResults }" 
										method="get"
										>
											<script>
												var autocomplete = <@ autocomplete.php @>
											</script>
											<div 
											class="uk-autocomplete uk-width-1-1" 
											data-uk-autocomplete='{source:autocomplete,minLength:2}'
											>
												<input 
												class="uk-form-controls uk-form-large uk-width-1-1" 
												type="search" 
												name="search" 
												placeholder="@{ placeholderSearch | def ('Search') }" 
												required 
												/>	
											</div>
										</form>	
									</div>
								<@ end ~@>
								<div class="uk-block @{ :classNav }">
									<@ tree.php @>
								</div>
							</div>
						</div>
					</div>
			    </div>
			</nav>
		</div>
	</div>