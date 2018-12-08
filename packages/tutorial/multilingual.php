<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<# 
Like in all other tutorial templates, the header, 
navbar and content snippets are include first. 
#>
<@ snippets/header.php @>	
	<@ snippets/navbar.php @>
	<section class="section">		
		<div class="content">
			<h1>@{ title }</h1>
			<div class="is-size-5">
				<#
				Storing the language selection in the session data array.
				In case the query string has a 'lang' parameter, a session data variable '%lang'
				is defined. Note that all session data variables begin with a '%'. 
				#>
				<@ if @{ ?lang } @>
					<@ set { %lang: @{ ?lang | 5 } } @>
				<@ end @>
				<#
				In case %lang is set to 'de' and a german text exists, 
				the german text will be displayed. The english text is always the fallback. 
				#>
				<@ if @{ %lang } = 'de' and @{ textTeaserGerman } @>
					@{ textTeaserGerman | markdown }
				<@ else @>
					@{ textTeaser | markdown }
				<@ end @>
			</div>
		</div>
		<div class="field">
			<a href="#source" class="button is-light">Jump to Source</a>
		</div>	
	</section>
	<section class="section">
		<#
		This is the markup for the language selection buttons. 
		#>
		<div class="field is-grouped is-grouped-multiline is-marginless">
			<div class="control">
				<div class="field has-addons">
					<p class="control">
						<a
						href="?lang=en" 
						class="button is-info<@ if @{ %lang | def('en') } = 'en' @> is-active<@ end @>"
						>
							English
						</a>
					</p>
					<p class="control">
						<a
						href="?lang=de" 
						class="button is-info<@ if @{ %lang } = 'de' @> is-active<@ end @>"
						>
							German
						</a>
					</p>	
				</div>
			</div>
			<div class="control">
				<div class="field">
					<p class="control">
						<a
						href="@{ url }" 
						class="button is-light"
						>
							Use Session Setting
						</a>
					</p>
				</div>
			</div>
		</div>
	</section>
<# As the last step, the footer markup is included. #>
<@ snippets/footer.php @>