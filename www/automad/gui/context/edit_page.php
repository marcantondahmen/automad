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
 *	The GUI page to edit page content. As part of the GUI, this file is only to be included from the Gui class.
 */


if (array_key_exists(Parse::queryKey('url'), $this->collection)) {
	
	$P = $this->collection[Parse::queryKey('url')];
	$data = Parse::textFile($this->pageFile($P));

	if (!isset($data[AM_KEY_TITLE]) || !$data[AM_KEY_TITLE]) {
		$data[AM_KEY_TITLE] = basename($P->url);
	}

	$this->guiTitle = $this->guiTitle . ' / ' . $data[AM_KEY_TITLE];

} else {
	
	$P = false;

}


$this->element('header');


?>

		<div class="row">
			<div class="col-md-12">
				<?php $this->element('title'); ?>
			</div>
		</div>

		<div class="row">

			<div class="col-md-4">
				<?php $this->element('navigation');?> 
			</div>
			
			<div class="col-md-8">
					
				<?php if ($P) { ?>
			
				<div class="list-group">
			
					<a class="list-group-item" href="<?php echo AM_BASE_URL . $P->url; ?>" target="_blank"><h4><?php echo $P->url; ?></h4></a>
					
					<div class="list-group-item">
						
						<ul class="nav nav-pills nav-justified">
							
							<li><a href="<?php echo AM_BASE_URL . $P->url; ?>" target="_blank"><span class="glyphicon glyphicon-eye-open"></span> Visit Page</a></li>
							
							<!-- Add Subpage Button -->
							<li><a href="#" data-toggle="modal" data-target="#automad-add-subpage-modal"><span class="glyphicon glyphicon-plus"></span> Add Subpage</a></li>
							
							<?php if ($P->path != '/') { ?>
								 
							<!-- Move Page Button -->
							<li><a href="#" data-toggle="modal" data-target="#automad-move-page-modal"><span class="glyphicon glyphicon-arrow-right"></span> Move Page</a></li>
							
							<!-- Delete Page Button -->
							<li><a href="#" data-toggle="modal" data-target="#automad-delete-page-modal"><span class="glyphicon glyphicon-trash"></span> Delete Page</a></li>
							
							<?php } ?> 
							
						</ul>
						
						<!-- Add Subpage Modal -->
						<div class="modal fade" id="automad-add-subpage-modal">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title">Add Subpage</h4>
									</div>
									<form class="automad-form" data-automad-handler="add_page" data-automad-url="<?php echo $P->url; ?>" role="form">
										<div class="modal-body">
											<div class="form-group">
												<label for="add-subpage-title" class="text-muted">Title</label>
												<input id="add-subpage-title" class="form-control" type="text" name="subpage[<?php echo AM_KEY_TITLE; ?>]" value="" onkeypress="return event.keyCode != 13;" required />
											</div>
											<?php echo $this->templateSelectBox('add-subpage-theme_template', 'subpage[theme_template]'); ?>
										</div>
										<div class="modal-footer">
											<div class="btn-group">
												<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
												<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Add Subpage</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						
						<!-- Move Page Modal -->
						<div class="modal fade" id="automad-move-page-modal" data-automad-url="<?php echo $P->url; ?>" data-automad-title="<?php echo $data[AM_KEY_TITLE]; ?>">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title">Move Page</h4>
									</div>
									<div class="modal-body">
										<h5>Select a destination:</h5>
										<?php echo $this->siteTree('', $this->collection, $P->url, array(), true); ?>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
									</div>
								</div>
							</div>
						</div>
						
						<!-- Delete Page Confirm Modal -->
						<div class="modal fade" id="automad-delete-page-modal">
							<div class="modal-dialog modal-sm">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title">Delete Page</h4>
									</div>
									<form class="automad-form" data-automad-handler="delete_page" data-automad-url="<?php echo $P->url; ?>" role="form">
										<input type="hidden" name="title" value="<?php echo $data[AM_KEY_TITLE]; ?>" />
										<div class="modal-body">
											<h4>Are you sure you want to delete <strong><?php echo $data[AM_KEY_TITLE]; ?></strong>?</h4>
											<p>The page and all of its subpages will be moved to <strong><?php echo AM_DIR_TRASH; ?></strong>, in case you want to restore the page's directory later.</p>
										</div>
										<div class="modal-footer">
											<div class="btn-group">
												<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
												<button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span> Delete</button>
											</div>
										</div>
									</form>	
								</div>
							</div>
						</div>
					
					</div>
					
					<div class="list-group-item">
						<!-- Nav tabs -->
						<ul class="nav nav-pills nav-justified">
							<li class="active"><a href="#data" data-toggle="tab"><span class="glyphicon glyphicon-align-left"></span> Data &amp; Settings</a></li>
							<li><a href="#files" data-toggle="tab"><span class="glyphicon glyphicon-picture"></span> Files</a></li>
						</ul>
					</div>
					
				</div>	
				
				<!-- Tab panes -->
				<div class="tab-content">
					<div id="data" class="tab-pane fade in active">
						<form class="clearfix automad-form automad-init" data-automad-handler="page_data" data-automad-url="<?php echo $P->url; ?>" role="form"></form>
					</div>
					<div id="files" class="tab-pane fade">
						<script src="<?php echo AM_BASE_URL; ?>/automad/lib/jquery-file-upload/jquery.ui.widget.js" type="text/javascript" charset="utf-8"></script>
						<script src="<?php echo AM_BASE_URL; ?>/automad/lib/jquery-file-upload/jquery.fileupload.js" type="text/javascript" charset="utf-8"></script>
						<script src="<?php echo AM_BASE_URL; ?>/automad/lib/jquery-file-upload/jquery.iframe-transport.js" type="text/javascript" charset="utf-8"></script>
						<form class="clearfix automad-form automad-init" data-automad-handler="files" data-automad-url="<?php echo $P->url; ?>" role="form"></form>
					</div>
				</div>
				
				<?php } else { ?>
				
				<div class="alert alert-danger"><h4>Page "<?php echo Parse::queryKey('url');?>" not found!</h4></div>	
				
				<?php } ?>
						
			</div>
			
		</div>

<?php


$this->element('footer');


?>