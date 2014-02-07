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

/**
 *	The Automad GUI footer.
 *	The footer expects the property $this->message to be used as an alert message.
 */


namespace Core;


defined('AUTOMAD') or die('Direct access not permitted!');


?>
</div> <!-- close main -->

<div class="footer"><?php if ($this->user()) {
	echo 'Version ' . AM_VERSION . ' | Logged in as <b>' . ucwords($this->user()) . '</b>';
} ?></div>

</div> <!-- close wrapper -->

<?php if ($this->modalDialogContent) { ?>
<script>
	$('<div><span class="text"><?php echo $this->modalDialogContent; ?></span></div>').dialog({
		title: '<?php echo $this->guiTitle; ?>', 
		width: 300, 
		position: { 
			my: 'center', 
			at: 'center top+35%', 
			of: window 
		}, 
		resizable: false, 
		modal: true, 
		buttons: {
			Ok: function() {
				$(this).dialog('close');
			}
		}
	});	
</script>
<?php }


// Display execution time, user constants and server info
Debug::timerEnd();
Debug::uc();
Debug::log('Server:');
Debug::log($_SERVER);	

		
?>
</body>
</html>