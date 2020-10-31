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
 *	Copyright (c) 2014-2020 by Marc Anton Dahmen
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
if ($delete = \Automad\Core\Request::post('delete')) {	
	$output = Accounts::delete($delete);
} else {
	$output = array();
}


ob_start();

?>
<ul class="uk-grid uk-grid-width-medium-1-4" data-uk-grid-margin>
<?php

foreach (Accounts::get() as $user => $hash) { 
	
	$id = 'am-user-' . \Automad\Core\Str::sanitize($user);

	?>
		<li>
			<div id="<?php echo $id; ?>" class="uk-panel uk-panel-box">
				<div class="uk-margin-small-bottom">
					<i class="uk-icon-user uk-icon-medium"></i>
				</div>
				<div class="uk-margin-small-bottom">
					<?php echo ucwords($user); ?>
				</div>
				<div class="am-panel-bottom">
					<div class="am-panel-bottom-right">
						<?php if ($user != User::get()) { ?>
							<label class="am-toggle-checkbox am-panel-bottom-link" data-am-toggle="#<?php echo $id; ?>">
								<input type="checkbox" name="delete[]" value="<?php echo $user; ?>" />
							</label>
						<?php } else { ?>
							<div class="am-panel-bottom-link uk-text-muted"><?php Text::e('sys_user_you'); ?></div>
						<?php } ?>
					</div>
				</div>
			</div>	
		</li>
	<?php 

}

?>
</ul>
<?php

$output['html'] = ob_get_contents();
ob_end_clean();


$this->jsonOutput($output);


?>