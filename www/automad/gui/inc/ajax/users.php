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
 *	Copyright (c) 2014-2017 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	List of registered users with the option to delete selected.
 */


// Delete selected users.
if (isset($_POST['delete'])) {	
	$output = Accounts::delete($_POST['delete']);
} else {
	$output = array();
}


ob_start();


foreach (Accounts::get() as $user => $hash) { 
	
?>
		
	<div class="uk-panel uk-panel-box uk-margin-small-bottom uk-margin-small-top">
		<span class="uk-hidden-small"><i class="uk-icon-user"></i>&nbsp;</span>
		<?php echo $user; ?>
		<div class="uk-float-right">
			<?php if ($user != User::get()) { ?>
			<label class="am-toggle-checkbox" data-am-toggle>
				<input type="checkbox" name="delete[]" value="<?php echo $user; ?>" />
			</label>
			<?php } else { ?>
			<?php Text::e('sys_user_you'); ?>
			<?php } ?>
		</div>
	</div>	
	
<?php 

}


$output['html'] = ob_get_contents();
ob_end_clean();


echo json_encode($output);


?>