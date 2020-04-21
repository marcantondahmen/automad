<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
	<div class="navbar">
		<div class="uk-container uk-container-center">
			<nav class="uk-navbar">
				<a href="/" class="uk-navbar-brand"><@ 
					with @{ imageLogo } 
						@><img 
						src="<@ with @{ :file } { height: @{ logoHeight | def (40) } } @>@{ :fileResized }<@ end @>" 
						srcset="<@ with @{ :file } { height: @{ logoHeight | def (40) | *2 } } @>@{ :fileResized } 2x<@ end @>"
						alt="@{ :basename }"
						><@ 
					else 
						@>@{ brand | def (@{ sitename }) }<@ 
					end 
				@></a>
			    <div class="uk-navbar-flip">
			        <ul class="uk-navbar-nav uk-visible-large">
						<@ newPagelist { excludeHidden: false } @>
						<@ foreach in pagelist ~@>
							<@ if @{ checkboxShowInNavbar } ~@>
							<li<@ if @{ :current } @> class="uk-active"<@ end @>>
								<a href="@{ url }">@{ title }</a>
							</li>
							<@~ end @>
						<@~ end @>				
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
		<# Modal with site tree and search. #>
		<div id="modal-nav" class="uk-modal">		
			<div class="uk-modal-dialog uk-modal-dialog-blank">
				<div class="uk-container uk-container-center navbar-push">
					<div class="uk-block">
						<# Search. #>
						<@ if @{ urlSearchResults } @>
							<form 
							class="uk-form uk-margin-large-bottom" 
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
									class="uk-form-controls uk-width-1-1" 
									type="search" 
									name="search" 
									placeholder="@{ placeholderSearch | def ('Search') }" 
									required 
									/>	
								</div>
							</form>	
						<@ end @>
						<@ tree.php @>
					</div>
				</div>
			</div>
		</div>
	</div>