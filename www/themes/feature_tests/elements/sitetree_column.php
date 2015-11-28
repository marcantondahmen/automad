{@ pagelist { type: "siblings" } @}
<div class="col-md-3">
	<div class="list-group">
	{@ foreach in pagelist @}
	{@ pagelist { type: "children", parent: {[ url ]} } @}
	<a class="level-{[ :level ]} list-group-item{@ if {[ :current-path ]} @} current-path list-group-item-info{@ end @}{@ if {[ :current ]} @} current active{@ end @}" href="{[ url ]}">
		<span class="glyphicon glyphicon-folder-{@ if {[ :current-path ]} @}open{@ else @}close{@ end @}" aria-hidden="true"></span>&nbsp;
		{[ title ]}
		{@ if {[ :pagelist-count ]} @}<span class="badge">{[ :pagelist-count ]}</span>{@ end @}
	</a>	
	{@ end @}
	</div>
</div>
{@ pagelist { type: "children", parent: {[ url ]} } @}
{@ foreach in pagelist @}
	{@ if {[ :current-path ]} @}
	{@ sitetree_column.php @}
	{@ end @}
{@ end @}