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

			<?php $this->element('title'); ?> 
			<div class="list-group">
				<div class="list-group-item">
					<ul class="nav nav-pills nav-stacked">	
						<li class="<?php if (Parse::queryKey('context') == 'system_settings') { echo ' active'; }?>"><a href="?context=system_settings"><span class="glyphicon glyphicon-cog"></span> <?php echo $this->tb['sys_title']; ?></a></li>
					</ul>
				</div>		
				<div class="list-group-item">
					<ul class="nav nav-pills nav-stacked">				
						<li class="<?php if (Parse::queryKey('context') == 'edit_shared') { echo ' active'; }?>"><a href="?context=edit_shared"><span class="glyphicon glyphicon-globe"></span> <?php echo $this->tb['shared_title']; ?></a></li>	
					</ul>
				</div>		
				<div class="list-group-item pages">	
					<?php echo $this->siteTree('', $this->collection, Parse::queryKey('url'), array('context' => 'edit_page'), false); ?> 	
				</div>			
				<div class="list-group-item">
					<ul class="nav nav-pills nav-stacked">		
						<li><a href="?context=logout"><span class="glyphicon glyphicon-off"></span> <?php echo $this->tb['log_out_title']; ?> "<?php echo ucwords($this->user()); ?>"</a></li>
					</ul>
				</div>
			</div>
