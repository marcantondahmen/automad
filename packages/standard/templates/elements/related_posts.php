<@~ newPagelist { type: 'related', sort: @{ sortRelatedPages | def ('date desc') } } @>	
<@~ if @{ :pagelistCount } and not @{ checkboxHideRelatedPages } @>
	<@ related.php @>
	<section <@ if @{ :pagelistDisplayCount } < 3 @>class="am-block"<@ end @>>
		<@ if @{ checkboxUseAlternativePagelistLayout } @>
			<@ ../blocks/pagelist/blog_alt.php @>
		<@ else @>
			<@ ../blocks/pagelist/blog.php @>
		<@ end @>
	</section>
<@ end ~@>