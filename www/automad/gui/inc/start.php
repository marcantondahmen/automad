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


namespace Automad\GUI;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	The GUI Start Page. As part of the GUI, this file is only to be included via the GUI class.
 */


$Cache = new \Automad\Core\Cache();
$lastEdit = $Cache->getSiteMTime();


$this->guiTitle = $this->guiTitle . ' / ' . Text::get('start_title') . ' ' . ucwords(User::get());
$this->element('header');


?>
		
		<div class="column content">
			<div class="inner">
				<div class="start">
					<h1><?php echo Text::get('start_title') . ' ' . ucwords(User::get()); ?></h1>
					<h4><?php echo Text::get('start_last_edit'); ?> <span class="badge"><?php echo date('j. F Y, G:i', $lastEdit); ?>h</span></h4>
					<h4><span class="automad-status" data-automad-status="cache"></span></h4>
					<h4><span class="automad-status" data-automad-status="debug"></span></h4>
					<h4>Automad Version <span class="badge"><?php echo AM_VERSION; ?></span></h4>
				</div>	
			</div>	
		</div>

<?php


$this->element('footer');


?>