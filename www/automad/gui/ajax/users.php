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
 *	List of registered users with the option to delete selected.
 */


$output = array();
$output['debug'] = $_POST;


$accounts = unserialize(file_get_contents(AM_FILE_ACCOUNTS));


// Delete selected users.
if (isset($_POST['delete'])) {
	
	if (is_writable(AM_FILE_ACCOUNTS)) {
	
		$deleted = array();
	
		foreach ($_POST['delete'] as $userToDelete) {
		
			if (isset($accounts[$userToDelete])) {
			
				unset($accounts[$userToDelete]);
				$deleted[] = $userToDelete;
			
			}
		
		}

		// Write array with all accounts back to file.
		if (file_put_contents(AM_FILE_ACCOUNTS, serialize($accounts))) {
			$output['success'] = 'Successfully deleted <strong>' . implode(', ', $deleted) . '</strong>';
		}
		
	} else {
		
		$output['error'] = 'Error while deleting the selected users!';
		
	}
	
}


ob_start();


?>
	
	<div class="modal-body clearfix">	
		
		<?php foreach ($accounts as $user => $hash) { ?>	

		<div class="row">	
	
			<h5 class="col-xs-2"><span class="glyphicon glyphicon-user"></span></h5>
			<h5 class="col-xs-8"><?php echo $user; ?></h5>
			<div class="col-xs-2">
			<?php if ($user != $this->user()) { ?>
				<div class="pull-right btn-group" data-toggle="buttons">
					<label class="btn btn-default" title="Mark user for deletion">
						<input type="checkbox" name="delete[]" value="<?php echo $user; ?>"><span class="glyphicon glyphicon-trash"></span>
					</label>
				</div>
			<?php } ?>	
			</div>
			
		</div>	
		
		<div class="row"><hr></div>	
	
		<?php } ?>	
			
		<div class="btn-group pull-right">
			<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Close</button>
			<button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span> Remove</button>
		</div>
		
	</div>

<?php


$output['html'] = ob_get_contents();
ob_end_clean();


echo json_encode($output);


?>