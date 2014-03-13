<?php 
/*
 *	                  ....
 *	                .:   '':.
 *	                ::::     ':..
 *	                ::.         ''..
 *	     .:'.. ..':.:::'    . :.   '':.
 *	    :.   ''     ''     '. ::::.. ..:
 *	    ::::.        ..':.. .''':::::  .
 *	    :::::::..    '..::::  :. ::::  :
 *	    ::'':::::::.    ':::.'':.::::  :
 *	    :..   ''::::::....':     ''::  :
 *	    :::::.    ':::::   :     .. '' .
 *	 .''::::::::... ':::.''   ..''  :.''''.
 *	 :..:::'':::::  :::::...:''        :..:
 *	 ::::::. '::::  ::::::::  ..::        .
 *	 ::::::::.::::  ::::::::  :'':.::   .''
 *	 ::: '::::::::.' '':::::  :.' '':  :
 *	 :::   :::::::::..' ::::  ::...'   .
 *	 :::  .::::::::::   ::::  ::::  .:'
 *	  '::'  '':::::::   ::::  : ::  :
 *	            '::::   ::::  :''  .:
 *	             ::::   ::::    ..''
 *	             :::: ..:::: .:''
 *	               ''''  '''''
 *	
 *
 *	AUTOMAD CMS
 *
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


namespace Core;


defined('AUTOMAD') or die('Direct access not permitted!');


?>
			
				<div class="list-group">
					
					<a class="list-group-item<?php if (Parse::queryKey('context') == 'system_settings') { echo ' active'; }?>" href="?context=system_settings"><h5><span class="glyphicon glyphicon-cog"></span> System Settings</h5></a>
					<a class="list-group-item<?php if (Parse::queryKey('context') == 'edit_shared') { echo ' active'; }?>" href="?context=edit_shared"><h5><span class="glyphicon glyphicon-globe"></span> Global Content &amp; Settings</h5></a>	
					<div class="list-group-item<?php if (Parse::queryKey('context') == 'edit_page') { echo ' list-group-item-info'; }?>"><?php echo $this->siteTree('', $this->collection, Parse::queryKey('url'), array('context' => 'edit_page'), false); ?></div>
					<a class="list-group-item" href="?context=logout"><h5 class="text-muted"><span class="glyphicon glyphicon-off"></span> Log Out "<?php echo ucwords($this->user()); ?>"</h5></a>
				
				</div>
			