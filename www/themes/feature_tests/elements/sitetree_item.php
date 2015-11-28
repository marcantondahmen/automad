{@ pagelist { type: "children", parent: {[ url ]}, sortOrder: "asc" } @}
<a class="level-{[ :level ]} list-group-item{@ if {[ :current-path ]} @} list-group-item-info{@ end @}{@ if {[ :current ]} @} active{@ end @}" href="{[ url ]}">
	{[ title ]}
	{@ if {[ :pagelist-count ]} @}<span class="badge">{[ :pagelist-count ]}</span>{@ end @}
</a>
{@ if {[ :pagelist-count ]} @}
<div class="list-group-item{@ if {[ :current-path ]} @} list-group-item-info{@ end @}">
	<div class="list-group">
		{@ foreach in pagelist @}
		{@ sitetree_item.php @}
		{@ end @}
	</div>
</div>
{@ end @}
