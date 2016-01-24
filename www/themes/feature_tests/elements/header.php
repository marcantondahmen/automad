<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	{@ metaTitle @}	
	{@ jQuery @}
	{@ bootstrapJS @}
	{@ bootstrapCSS @}
</head>

<body style="padding-top: 90px;">
	
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<a class="navbar-brand" href="/">{[ sitename ]}</a>
			</div>
			<ul class="nav navbar-nav">
				{@ pagelist { type: "children", parent: "/", sortOrder: "asc" } @}
				{@ foreach in pagelist @}
				<li{@ if {[ :current ]} @} class="active"{@ end @}><a href="{[ url ]}">{[ title ]}</a></li>
				{@ end @}
			</ul>	
		</div>
	</nav>
	
	<div class="container">
			
		<div class="row">
			<div class="col-md-12">
				<div class="btn-group btn-group-justified">
				{@ pagelist { type: false, sortOrder: "asc" } @}
				{@ foreach in pagelist @}
					{@ if {[ theme ]} = "feature_tests" @}
					<a class="btn btn-default{@ if {[ :current ]} @} active{@ end @}" href="{[ url ]}">{[ title ]}</a>
					{@ end @}
				{@ end @}
				</div>
				<nav>
					<ul class="pager">
						{@ pagelist { type: 'siblings' } @}
						{@ with prev @}<li class="previous"><a href="{[ url ]}"><span aria-hidden="true">&larr;</span> {[ title ]}</a></li>{@ end @}
						{@ with next @}<li class="next"><a href="{[ url ]}">{[ title ]} <span aria-hidden="true">&rarr;</span></a></li>{@ end @}
					</ul>
				</nav>
			</div>
		</div>
		