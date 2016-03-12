{@ elements/header.php @}

	{* Define objects *}
	{@ filelist { glob: "*.jpg", sortOrder: {[ ?sort | defaultValue ('asc') ]} } @}
	{@ pagelist { type: "children", parent: false } @}

	{* Snippets *}
	{@ snippet item @}
		<li>
			<h5>{[ title ]}</h5>
			<h6>{[ :filelist-count ]} files found</h6>
			<div class="clearfix">
			{@ foreach in filelist @}
				{@ img { file: {[ :file ]}, width: 150, class: "img-rounded img-thumbnail pull-left", link: {[ url ]} } @}
			{@ end @}
			</div>
			<ul>
			{@ if {[ :pagelist-count ]} @}<li><span class="badge">{[ :pagelist-count ]} pages found as children of "{[ :path ]}"</span></li>{@ end @}
			{@ foreach in pagelist @}
				{@ item @}
			{@ end @}
			</ul>
		</li>
	{@ end @}
	
	<div class="row">
		
		<div class="col-md-12">
			<h1>{[ title ]}</h1>
			<hr>
			<a class="btn btn-default{@ if {[ ?sort | defaultValue ('asc') ]} = "asc" @} active{@ end @} btn-sm" href="?sort=asc">Ascending</a> 
			<a class="btn btn-default{@ if {[ ?sort ]} = "desc" @} active{@ end @} btn-sm" href="?sort=desc">Descending</a>
			{@ with "/" @}
			<ul>
				{@ item @}
			</ul>	
			{@ end @}
		</div>
		
	</div>

{@ elements/footer.php @}