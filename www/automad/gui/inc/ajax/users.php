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


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	List of registered users with the option to delete selected.
 */


$output = array();


$accounts = $this->accountsGetArray();


// Delete selected users.
if (isset($_POST['delete'])) {
	
	// Only delete users from list, if accounts.txt is writable.
	// It is important, to verify write access here, to make sure that all accounts stored in account.txt are also returned in the HTML.
	// Otherwise, they would be deleted from the array without actually being deleted from the file, in case accounts.txt is write protected.
	// So it is not enough to just check, if file_put_contents was successful, because that would be simply too late.
	if (is_writable(AM_FILE_ACCOUNTS)) {
	
		$deleted = array();
	
		foreach ($_POST['delete'] as $userToDelete) {
		
			if (isset($accounts[$userToDelete])) {
			
				unset($accounts[$userToDelete]);
				$deleted[] = $userToDelete;
			
			}
		
		}

		// Write array with all accounts back to file.
		if ($this->accountsSaveArray($accounts)) {
			$output['success'] = $this->tb['success_remove'] . ' <strong>' . implode(', ', $deleted) . '</strong>';
		}
		
	} else {
		
		$output['error'] = $this->tb['error_permission'] . '<p>' . AM_FILE_ACCOUNTS . '</p>';
		
	}
	
}


ob_start();


?>
	
	<div class="modal-body">	
		<?php foreach ($accounts as $user => $hash) { ?>	
		<div class="box">
			<div class="row">	
				<div class="col-xs-10"><h4><span class="glyphicon glyphicon-user"></span> <?php echo $user; ?></h4></div>
				<div class="col-xs-2">
				<?php if ($user != $this->user()) { ?>
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
				<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> <?php echo $this->tb['btn_close']; ?></button>
			</div>
			<div class="btn-group">
				<button type="submit" class="btn btn-danger" data-loading-text="<?php echo $this->tb['btn_loading']; ?>"><span class="glyphicon glyphicon-trash"></span> <?php echo $this->tb['btn_remove_selected']; ?></button>
			</div>
		</div>
	</div>

<?php


$output['html'] = ob_get_contents();
ob_end_clean();


echo json_encode($output);


?>