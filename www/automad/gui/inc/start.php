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
 *	AUTOMAD
 *
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	The GUI Start Page. As part of the GUI, this file is only to be included via the GUI class.
 */


$Cache = new Cache();
$lastEdit = $Cache->getSiteMTime();


$this->guiTitle = $this->guiTitle . ' / ' . $this->tb['start_title'] . ' ' . ucwords($this->user());
$this->element('header');


?>
		
		<div class="column content">
			<div class="inner">
				<div class="start">
					<h1><?php echo $this->tb['start_title'] . ' ' . ucwords($this->user()); ?></h1>
					<h4><?php echo $this->tb['start_last_edit']; ?> <span class="badge"><?php echo date('j. F Y, G:i', $lastEdit); ?>h</span></h4>
					<h4><span class="automad-status" data-automad-status="cache"></span></h4>
					<h4><span class="automad-status" data-automad-status="debug"></span></h4>
					<h4>Automad Version <span class="badge"><?php echo AM_VERSION; ?></span></h4>
				</div>	
			</div>	
		</div>

<?php


$this->element('footer');


?>