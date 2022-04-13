<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ newPagelist { sort: 'title asc, url asc' } @>[<@ 	
foreach in pagelist 
@><@ if @{ :i | -1 } @>,<@ end 
@>{"value": "@{ title }",<#
#>"url": "@{ :origUrl }",<#
#>"parent": "<@ with @{ :parent } @>@{ title }<@ end @>"}<@ 
end @>]