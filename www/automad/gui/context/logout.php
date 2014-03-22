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


$this->guiTitle = $this->guiTitle . ' / ' . $this->tb['log_out_title'];
$this->element('header');


?>

	<div class="column single">

		<div class="inner">
			
			<?php $this->element('title'); ?>
	
			<div class="list-group">
				
				<div class="list-group-item">
					<h4><?php echo $this->tb['success_log_out']; ?></h4>
				</div>
				
				<div class="list-group-item clearfix">
					<div class="pull-right">
						<a href="<?php echo AM_BASE_URL . AM_INDEX . AM_PAGE_GUI; ?>" class="btn btn-primary"><?php echo $this->tb['btn_login']; ?></a>
					</div>
				</div>
				
			</div>

		</div>

	</div>

<?php


$this->element('footer');


?>