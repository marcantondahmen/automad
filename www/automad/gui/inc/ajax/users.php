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
 *	List of registered users with the option to delete selected.
 */


// Delete selected users.
if (isset($_POST['delete'])) {	
	$output = Accounts::delete($_POST['delete']);
} else {
	$output = array();
}


ob_start();


?>
	
	<div class="modal-body">	
		<?php foreach (Accounts::get() as $user => $hash) { ?>	
		<div class="box">
			<div class="row">	
				<div class="col-xs-10"><h4><span class="glyphicon glyphicon-user"></span> <?php echo $user; ?></h4></div>
				<div class="col-xs-2">
				<?php if ($user != User::get()) { ?>
					<div class="pull-right btn-group" data-toggle="buttons">
						<label class="btn btn-default btn-xs">
							<input type="checkbox" name="delete[]" value="<?php echo $user; ?>"><span class="glyphicon glyphicon-ok"></span>
						</label>
					</div>
				<?php } ?>	
				</div>
			</div>	
		</div>	
		<?php } ?> 
	</div>

	<div class="modal-footer">			
		<div class="btn-group btn-group-justified">
			<div class="btn-group">
				<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> <?php echo Text::get('btn_close'); ?></button>
			</div>
			<div class="btn-group">
				<button type="submit" class="btn btn-danger" data-loading-text="<?php echo Text::get('btn_loading'); ?>"><span class="glyphicon glyphicon-trash"></span> <?php echo Text::get('btn_remove_selected'); ?></button>
			</div>
		</div>
	</div>

<?php


$output['html'] = ob_get_contents();
ob_end_clean();


echo json_encode($output);


?>