<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
	<section class="section">
		<div class="field is-grouped">
			<p class="control">
				<a 
				href="/" 
				class="button is-dark"
				>
					@{ sitename }
				</a>
			</p>
			<p>
				<div class="dropdown is-hoverable">
					<div class="dropdown-trigger">
						<button class="button is-white" aria-haspopup="true" aria-controls="dropdown-menu">
							<span>Automad Tutorials</span>
							<span class="icon is-small">
								<i class="fas fa-angle-down" aria-hidden="true"></i>
							</span>
						</button>
					</div>
					<div class="dropdown-menu" id="dropdown-menu" role="menu">
						<div class="dropdown-content">
							<a href="/tutorials" class="dropdown-item">
								Introduction
							</a>
							<hr class="dropdown-divider" />
							<@ newPagelist { 
								type: 'children',
								context: '/tutorials',
								excludeHidden: false
							} @>
							<@ foreach in pagelist @>
								<a href="@{ url }" class="dropdown-item">
									@{ title }
								</a>
							<@ end @>
						</div>
					</div>
				</div>
			</p>
		</div>
	</section>