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

		<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
			<div class="container-fluid">
				<div class="navbar-left">
					<?php $this->element('title'); ?> 
				</div>
				<ul class="nav navbar-nav navbar-right">
					<li><a href="<?php echo AM_BASE_URL; ?>/" target="_blank"><span class="glyphicon glyphicon-home"></span> <?php echo $this->siteName(); ?></a></li>
					<li><a href="http://automad.org" target="_blank"><span class="glyphicon glyphicon-question-sign"></span> <?php echo $this->tb['btn_docs']; ?></a></li>
					<?php if ($this->user()) { ?><li><a href="?context=logout"><span class="glyphicon glyphicon-off"></span> <?php echo $this->tb['log_out_title']; ?> "<?php echo ucwords($this->user()); ?>"</a></li><?php } ?> 
				</ul>
	    		</div>
		</nav>

		<?php if ($this->user()) { ?> 
		<div class="column nav">
			<ul class="nav nav-pills nav-stacked">	
				<li class="<?php if (Parse::queryKey('context') == 'system_settings') { echo ' active'; }?>">
					<a href="?context=system_settings"><span class="glyphicon glyphicon-cog"></span> <?php echo $this->tb['sys_title']; ?></a>
				</li>			
				<li class="<?php if (Parse::queryKey('context') == 'edit_shared') { echo ' active'; }?>">
					<a href="?context=edit_shared"><span class="glyphicon glyphicon-globe"></span> <?php echo $this->tb['shared_title']; ?></a>
				</li>	
			</ul>
			<div class="pages">
				<?php echo $this->siteTree('', $this->collection, array('context' => 'edit_page'), false); ?>
			</div>	
		</div>
		<?php } ?> 
			