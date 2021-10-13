<@~ newPagelist { type: 'related', sort: @{ sortRelatedPages | def ('date desc') } } @>	
<@~ if @{ :pagelistCount } and not @{ checkboxHideRelatedPages } @>
	<@ related.php @>
	<section <@ if @{ :pagelistDisplayCount } < 4 @>class="am-block"<@ end @>>
		<@ if @{ checkboxUseAlternativePagelistLayout } @>
			<@ ../blocks/pagelist/portfolio_alt.php @>
		<@ else @>
			<@ ../blocks/pagelist/portfolio.php @>
		<@ end @>
	</section>
<@ end ~@>