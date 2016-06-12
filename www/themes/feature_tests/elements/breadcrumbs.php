<ol class="breadcrumb">
	<@ pagelist { type: "breadcrumbs" } @>
	<@ foreach in pagelist @>
	<li><a href="@{url}">@{title}</a></li>	
	<@ end @>
</ol>