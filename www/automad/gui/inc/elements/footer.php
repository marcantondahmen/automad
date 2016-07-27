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
 *	Copyright (c) 2014-2016 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;


defined('AUTOMAD') or die('Direct access not permitted!');


?>
		
		<!-- Footer -->
		<div class="automad-footer uk-position-bottom uk-text-muted">
			<?php if (User::get()) { ?>
			<ul class="uk-grid uk-grid-width-1-1 uk-grid-width-large-1-2">
				<li><i class="uk-icon-automad"></i>&nbsp;&nbsp;<?php echo AM_VERSION; ?></li>
				<li class="uk-text-right uk-text-left-medium"><i class="uk-icon-user"></i>&nbsp;&nbsp;<?php echo User::get(); ?></li>
			</ul>
			<?php } else { ?>
				<div class="uk-text-center">
					<i class="uk-icon-automad"></i>
				</div>
			<?php } ?>
		</div>
		
	</div> <!-- .uk-container -->	
	
	<!-- No-JS -->
	<div id="automad-no-js" class="uk-block uk-block-primary uk-contrast">
		<div class="uk-container uk-container-center">
			<i class="uk-icon-warning uk-icon-large"></i>
			<?php echo Text::get('error_no_js'); ?>
		</div>
	</div>
	
</body>
</html>