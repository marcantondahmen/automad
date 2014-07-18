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


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


?>

		<?php if ($this->user()) { ?> 
		<div class="column nav">
			<div class="fixed">
				<ul class="nav nav-pills nav-stacked">	
					<li class="<?php if (Parse::queryKey('context') == 'system_settings') { echo ' active'; }?>">
						<a href="?context=system_settings"><span class="glyphicon glyphicon-cog"></span> <?php echo $this->tb['sys_title']; ?></a>
					</li>			
					<li class="<?php if (Parse::queryKey('context') == 'edit_shared') { echo ' active'; }?>">
						<a href="?context=edit_shared"><span class="glyphicon glyphicon-globe"></span> <?php echo $this->tb['shared_title']; ?></a>
					</li>	
				</ul>
				<?php echo $this->siteTree('', $this->collection, array('context' => 'edit_page'), false); ?> 
			</div>
		</div>
		<?php } ?> 
			