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
 *	The GUI Log Out Page. As part of the GUI, this file is only to be included via GUI::context().
 */


unset($_SESSION);
session_destroy();


$this->guiTitle = $this->guiTitle . ' / Logged Out Successfully!';
$this->element('header');


?>

	<div class="row">

		<div class="col-md-4 col-md-offset-4">
			
			<?php $this->element('title'); ?>
	
			<div class="list-group">
				
				<div class="list-group-item">
					<h4>Logged Out!</h4>
				</div>
				
				<div class="list-group-item clearfix">
					<div class="pull-right">
						<a href="<?php echo AM_BASE_URL . AM_INDEX . AM_PAGE_GUI; ?>" class="btn btn-primary">Log in again</a>
					</div>
				</div>
				
			</div>

		</div>

	</div>

<?php


$this->element('footer');


?>