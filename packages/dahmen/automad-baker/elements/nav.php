<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
		
		<@ snippet arrow @>
			<a
			href="#"
			data-baker-nav-target="@{ url }" 
			class="baker-nav-arrow"
			>
				<i class="uk-icon-chevron-@{ :arrow }"></i>
			</a>
		<@ end @>

		<@ snippet link @>
			<a 
			<@ if @{ :current } and @{ :level } > 0
				@>class="uk-active"<@ 
			else 
				@><@ 
				if @{ :level } = 1 and @{ :currentPath }
					@>class="baker-nav-top-active"<@ 
				end @><@ 
			end @>
			href="@{ url }" 
			>
				@{ title }
			</a>
		<@ end @>
		
		<# 
		Top Nav. 
		#>
		<ul class="baker-nav baker-nav-top baker-nav-large">
			<@ newPagelist { type: 'children' } @>
			<@ with '/' @>
				<@ if not @{ hidden } @>
					<li>
						<@ link @>
					</li>	
				<@ end @>
				<@ foreach in pagelist @>		
					<li>
						<@ link @>
					</li>
				<@ end @>
			<@ end @>
		</ul>
		
		<# 
		Breadcrumb Nav. 
		Create new pagelist to test if the current page has children 
		and exclude it if not. 
		#>
		<@ set { :arrow: 'up'} @>
		<@ newPagelist { type: 'children'} @>
		<@ if @{ :pagelistCount } @>
			<@ newPagelist { 
				type: 'breadcrumbs', 
				context: @{ url } 
			} @>
		<@ else @>	
			<@ newPagelist { 
				type: 'breadcrumbs', 
				context: @{ url }, 
				excludeCurrent: true 
			} @>
		<@ end @>
		<@ if @{ :pagelistCount } > 2 and @{ :level } > 1 @>
			<ul class="baker-nav baker-nav-breadcrumbs baker-nav-large uk-margin-bottom">
				<@ foreach in pagelist @>
					<@ if @{ :level } > 0 @>
						<li>
							<@ link @>
							<@ if @{ :pagelistCount } and @{ :i } != @{ :pagelistCount } @>
								<@ arrow @>
							<@ end @>
						</li>
					<@ end @>
				<@ end @>
			</ul>
		<@ end @>
		
		<# 
		Siblings or Children. 
		#>
		<@ set { :arrow: 'right' } @>
		<@ if @{ :level } > 0 @>
			<@ newPagelist { type: 'children' } @>	
			<@ if not @{ :pagelistCount } and @{ :level } > 1 @>
				<@ newPagelist { type: 'siblings', excludeCurrent: false } @>
			<@ end @>
			<@ if @{ :pagelistCount } @>
				<ul class="baker-nav">
					<@ foreach in pagelist @>
						<# Reset pagelist type to get correct pagelistCount for children for each button. #>
						<@ newPagelist { type: 'children' } @>	
						<li>
							<@ link @>
							<# Hide arrow icon on top level navigation. #>
							<@ if @{ :pagelistCount } and @{ :level } > 1 @>
								<@ arrow @>
							<@ end @>
						</li>
					<@ end @>
				</ul>
			<@ end @>
		<@ end @>
		