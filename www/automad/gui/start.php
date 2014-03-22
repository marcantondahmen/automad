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


/**
 *	The GUI Start Page. As part of the GUI, this file is only to be included via the GUI class.
 */


$C = new Cache();
$lastEdit = $C->getSiteMTime();


$this->guiTitle = $this->guiTitle . ' / ' . $this->tb['start_title'] . ' ' . ucwords($this->user());
$this->element('header');


?>

			<div class="column nav">
				<?php $this->element('navigation'); ?>
			</div>
		
			<div class="column content">
				<div class="inner">
					<div class="list-group">
						<div class="list-group-item">
							<h3><?php echo $this->tb['start_title'] . ' ' . ucwords($this->user()); ?></h3>
						</div>
						<div class="list-group-item">	
							<?php echo $this->tb['start_last_edit']; ?> <strong><?php echo date('l, j. F Y, G:i', $lastEdit); ?>h</strong>
						</div>
						<div class="list-group-item">	
							<span class="automad-status" data-automad-status="cache"></span><br />
							<span class="automad-status" data-automad-status="debug"></span>
						</div>
						<div class="list-group-item">
							Automad Version: <strong><?php echo AM_VERSION; ?></strong>
						</div>
					</div>	
				</div>	
			</div>

<?php


$this->element('footer');


?>