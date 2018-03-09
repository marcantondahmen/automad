<?php defined('AUTOMAD') or die('Direct access not permitted!'); ?>
<@ newPagelist @>[<@ 	
foreach in pagelist 
@><@ if @{ :i | -1 } @>,<@ end @>{"value":"@{ title | stripTags }"}<@ 
end @>]