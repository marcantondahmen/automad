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
			<div class="columns is-multiline is-8 is-variable">
				<div class="column is-half">	
					<# 
					Since also the content markup is the same in every template,
					it is included here as a reusable snippet. 
					#>
					<@ snippets/content.php @>
					<br />
				</div>
				<div class="column is-half">
					<aside class="menu">
						<# 
						As a next step a recursive snippet is defined to create the navigation tree.
						The snippet definition doesn't produce any output. 
						Calling the snippet below in the homepage context will create 
						the actual navigation tree.
						#>
						<@ snippet navigation @>	
							<ul class="menu-list">
								<# 
								The 'foreach' statement initiates an iteration over all pages in the pagelist.
								Note that the pagelist will be configured in a later step below
								right before changing to the root context as the entry level of the tree. 
								#>
								<@ foreach in pagelist @>
									<li>
										<a
										<# 
										While iterating over the pages,  
										the page context changes automatically with every iteration.
										Therefore, the normal page variables can simply be used to 
										reference the content of the current page within the loop.
										#>
										href="@{ url }"	
										<# 
										The ':current' runtime variable makes it easy to verify
										whether the active item in the loop is also the currently requested 
										page.
										#>
										<@ if @{ :current } @>class="has-background-info has-text-white"<@ end @>
										>
											@{ title }
										</a>
										<# 
										The snippet is recursively called here. 
										Since the pagelist type will be set to 'children' (see below),
										the pagelist automatically contains always all children of the active 
										context (page) in the loop.
										#>
										<@ navigation @>
									</li>
								<@ end @>
							</ul>
						<@ end @>
						<# Before actually calling the snippet, the pagelist is configured. #>
						<@ newPagelist { 
							type: 'children',
							excludeHidden: false 
						} @>
						<# 
						To create the tree markup, the navigation snippet is called once initially
						within the context of the homepage.
						#>
						<@ with '/' @>
							<@ navigation @>
						<@ end @>
					</aside>
				</div>
			</div>
		</section>
<# As the last step, the footer markup is included. #>
<@ snippets/footer.php @>