<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<# 
Since the header markup is the same in all templates, 
it is stored in a separate snippet file and therefore can be reused
across multiple templates.
#>
<@ snippets/header.php @>
		<# The top navigation is included here. #>
		<@ snippets/navbar.php @>
		<section class="section">
			<# 
			Since also the content markup is the same in every template,
			it is included here as a reusable snippet. 
			#>
			<@ snippets/content.php @>
		</section>
		<section class="section">
			<# 
			As a next step, the pagelist is configured.
			Note that query string parameters get used as parameter values to make the pagelist
			controllable by a menu.
			#>
			<@ newPagelist {  
				filter: @{ ?filter }, 
				match: '{":level": "/(1|2)/"}',
				search: @{ ?search },
				sort: @{ ?sort | def ('date desc') }
			} @>
			<# 
			A simple filter menu lets the user filter the paglist dynamically.
			#>
			<div class="field is-grouped is-grouped-multiline is-marginless">
				<div class="control">
					<div class="field has-addons">
						<p class="control">
							<a 
							<#
							The first button in the menu resets the filter.
							Note that the 'queryStringMerge' method is used here for the href attribute value to only 
							modify the filter parameter within an existing query string without resetting other options. 
							#>
							href="?<@ queryStringMerge { filter: false } @>" 
							class="button is-info<@ if not @{ ?filter } @> is-active<@ end @>">
								All
							</a>
						</p>
						<# 
						The 'filters' object contains all available tags of pages in the pagelist.
						The 'foreach' statement can be used to iterate over that list to create a filter button
						for every tag.
						#>
						<@ foreach in filters @>
							<p class="control">
								<a 
								<# Note the 'queryStringMerge' method. #>
								href="?<@ queryStringMerge { filter: @{ :filter } } @>" 
								<# The 'if' condition can be used to test whether a button is active. #>
								class="button is-info<@ if @{ ?filter } = @{ :filter } @> is-active<@ end @>"
								>
									<# The ':filter' runtime variable contains the current tag within the loop. #>
									@{ :filter }
								</a>
							</p>
						<@ end @>
					</div>
				</div>
				<# The sorting menu. #>
				<div class="control">
					<div class="field has-addons">
						<p class="control">
							<a 
							<# The concept of creating the sorting menu is the same as for the filters. #>
							href="?<@ queryStringMerge { sort: 'date desc' } @>" 
							class="button is-info<@ if not @{ ?sort } or @{ ?sort } = 'date desc' @> is-active<@ end @>">
								<span class="icon is-small">
									<i class="fas fa-sort-numeric-down" aria-hidden="true"></i>
								</span>&nbsp;
								Date
							</a>
						</p>
						<p class="control">
							<a 
							href="?<@ queryStringMerge { sort: 'title asc' } @>" 
							class="button is-info<@ if @{ ?sort } = 'title asc' @> is-active<@ end @>"
							>
								<span class="icon is-small">
									<i class="fas fa-sort-alpha-up" aria-hidden="true"></i>
								</span>&nbsp;
								Title
							</a>
						</p>
					</div>
				</div>
				<# A normal form is used to create the keyword search field. #>
				<div class="control">
					<form action="" method="get">
						<input 
						class="input" 
						type="text" 
						<# 
						Note that the input name can be used as variable. 
						For example 'name="search"' will add a query string parameter 'search' on submission
						which can be used as '@{ ?search }' (see value attribute).
						#>
						name="search" 
						placeholder="Keyword" 
						value="@{ ?search }"
						/>
					</form>
				</div>
			</div>
			<br />
			<# The pagelist markup. #>
			<div class="columns is-multiline is-8 is-variable">
				<@ foreach in pagelist @>
					<# 
					While iterating over the pages,  
					the page context changes automatically with every iteration.
					Therefore, the normal page variables can simply be used to 
					reference the content of the current page within the loop.
					#>
					<div class="column is-one-quarter">
						<hr />
						<div class="field">
							<span class="is-size-5 has-text-weight-bold">@{ title }</span>
							<br />
							<span class="is-size-7">
								<# 
								Pipe functions can be used to modify the content of a variable.
								Here the date string is formatted to 'F Y' (month and year).
								#>
								@{ date | dateFormat ('F Y') }
							</span>
						</div>
						<div class="field is-size-6">
							<# 
							Multiple pipe functions can be chained. 
							Here, the first paragraph block of the "+main" avriable is used
							as teaser text. If there is no paragraph, the "textTeaser" variable is used as falback.
							Then all tags get stripped before shortening the content to 100 characters.
							#>
							@{ +main | findFirstParagraph | def(@{ textTeaser }) | 100 }
						</div>
						<a href="@{ url }" class="button is-light is-small">More</a>
					</div>
				<@ end @>
			</div>
		</section>
<# As the last step, the footer markup is included. #>
<@ snippets/footer.php @>