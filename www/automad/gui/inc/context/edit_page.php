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


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	The GUI page to edit page content. As part of the GUI, this file is only to be included from the Gui class.
 */


if (array_key_exists(Parse::queryKey('url'), $this->collection)) {
	
	$Page = $this->collection[Parse::queryKey('url')];
	$data = Parse::textFile($this->pageFile($Page));

	if (!isset($data[AM_KEY_TITLE]) || !$data[AM_KEY_TITLE]) {
		$data[AM_KEY_TITLE] = basename($Page->url);
	}
	
	if (isset($data[AM_KEY_URL])) {
		$url = $data[AM_KEY_URL];
	} else {
		$url = $Page->url;
	}

	$this->guiTitle = $this->guiTitle . ' / ' . $data[AM_KEY_TITLE];

} else {
	
	$Page = false;

}


$this->element('header');


?>

		<?php if ($Page) { ?> 
		<div class="column subnav">
			<div class="scroll">	
				<div class="inner">
					<ul class="nav nav-pills nav-stacked">
						<li>
							<a href="<?php echo Resolve::url($Page, $url); ?>" target="_blank">
								<span class="glyphicon glyphicon-arrow-right"></span><span class="hidden-md"> <?php echo $this->tb['btn_visit_page']; ?></span>
							</a>
						</li>
						<!-- Data -->
						<li class="active">
							<a href="#data" data-toggle="tab">
								<span class="glyphicon glyphicon-align-left"></span><span class="hidden-md"> <?php echo $this->tb['btn_data']; ?></span>
							</a>
						</li>
						<!-- Files -->
						<li>
							<a href="#files" data-toggle="tab">
								<span class="glyphicon glyphicon-folder-open"></span><span class="hidden-md"> <?php echo $this->tb['btn_files']; ?></span>
							</a>
						</li>
						<!-- Add Subpage Button -->
						<li>
							<a href="#" data-toggle="modal" data-target="#automad-add-subpage-modal">
								<span class="glyphicon glyphicon-plus"></span><span class="hidden-md"> <?php echo $this->tb['btn_add_page']; ?></span>
							</a>
						</li>
						<?php if ($Page->path != '/') { ?> 
						<!-- Move Page Button -->
						<li>
							<a href="#" data-toggle="modal" data-target="#automad-move-page-modal">
								<span class="glyphicon glyphicon-move"></span><span class="hidden-md"> <?php echo $this->tb['btn_move_page']; ?></span>
							</a>
						</li>
						<!-- Delete Page Button -->
						<li>
							<a href="#" data-toggle="modal" data-target="#automad-delete-page-modal">
								<span class="glyphicon glyphicon-trash"></span><span class="hidden-md"> <?php echo $this->tb['btn_delete_page']; ?></span>
							</a>
						</li>
						<?php } ?> 
					</ul>
				</div>	
			</div>		
		</div>
		
		<div class="column content">
			<div class="inner">
				<div class="alert alert-info">
					<a href="<?php echo Resolve::url($Page, $url); ?>" target="_blank"><span class="glyphicon glyphicon-link"></span> <?php echo $url; ?></a>
				</div>
				<!-- Tab panes -->
				<div class="tab-content">
					<div id="data" class="tab-pane fade in active">
						<form class="automad-form automad-init" data-automad-handler="page_data" data-automad-url="<?php echo $Page->url; ?>" role="form">
							<span class="glyphicon glyphicon-time"></span> <?php echo $this->tb['btn_loading']; ?>
						</form>
					</div>
					<div id="files" class="tab-pane fade">
						<script src="<?php echo AM_BASE_URL; ?>/automad/lib/jquery-file-upload/jquery.ui.widget.js" type="text/javascript" charset="utf-8"></script>
						<script src="<?php echo AM_BASE_URL; ?>/automad/lib/jquery-file-upload/jquery.fileupload.js" type="text/javascript" charset="utf-8"></script>
						<script src="<?php echo AM_BASE_URL; ?>/automad/lib/jquery-file-upload/jquery.iframe-transport.js" type="text/javascript" charset="utf-8"></script>
						<form class="automad-form automad-init" data-automad-handler="files" data-automad-url="<?php echo $Page->url; ?>" role="form"></form>
					</div>
				</div>	
			</div>
		</div>
		
		<!-- Add Subpage Modal -->
		<div class="modal fade" id="automad-add-subpage-modal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h3 class="modal-title"><?php echo $this->tb['btn_add_page']; ?></h3>
					</div>
					<form class="automad-form" data-automad-handler="add_page" data-automad-url="<?php echo $Page->url; ?>" role="form">
						<div class="modal-body">
							<div class="alert alert-info"><span class="glyphicon glyphicon-arrow-right"></span> <b><?php echo rtrim($Page->url, '/'); ?>/</b></div>
							<div class="form-group">
								<label for="add-subpage-title">Title</label>
								<input id="add-subpage-title" class="form-control" type="text" name="subpage[<?php echo AM_KEY_TITLE; ?>]" value="" onkeypress="return event.keyCode != 13;" required />
							</div>
							<?php echo $this->templateSelectBox('add-subpage-theme_template', 'subpage[theme_template]'); ?>
						</div>
						<div class="modal-footer">
							<div class="btn-group btn-group-justified">
								<div class="btn-group"><button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> <?php echo $this->tb['btn_close']; ?></button></div>
								<div class="btn-group"><button type="submit" class="btn btn-primary" data-loading-text="<?php echo $this->tb['btn_loading']; ?>"><span class="glyphicon glyphicon-plus"></span> <?php echo $this->tb['btn_add_page']; ?></button></div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- Move Page Modal -->
		<div class="modal fade" id="automad-move-page-modal" data-automad-url="<?php echo $Page->url; ?>" data-automad-title="<?php echo $data[AM_KEY_TITLE]; ?>">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h3 class="modal-title"><?php echo $this->tb['btn_move_page']; ?></h3>
					</div>
					<div class="modal-body">
						<div class="alert alert-info"><span class="glyphicon glyphicon-move"></span> <b><?php echo $Page->url; ?></b></div>
						<h4><?php echo $this->tb['page_move_destination']; ?></h4>
						<?php echo $this->siteTree('', $this->collection, array(), true); ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> <?php echo $this->tb['btn_close']; ?></button>
					</div>
				</div>
			</div>
		</div>

		<!-- Delete Page Confirm Modal -->
		<div class="modal fade" id="automad-delete-page-modal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h3 class="modal-title"><?php echo $this->tb['btn_delete_page']; ?></h3>
					</div>
					<form class="automad-form" data-automad-handler="delete_page" data-automad-url="<?php echo $Page->url; ?>" role="form">
						<input type="hidden" name="title" value="<?php echo $data[AM_KEY_TITLE]; ?>" />
						<div class="modal-body">
							<div class="alert alert-danger"><span class="glyphicon glyphicon-trash"></span> <b><?php echo $Page->url; ?></b></div>
							<?php echo $this->tb['page_confirm_delete']; ?>  
						</div>
						<div class="modal-footer">
							<div class="btn-group btn-group-justified">
								<div class="btn-group"><button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> <?php echo $this->tb['btn_close']; ?></button></div>
								<div class="btn-group"><button type="submit" class="btn btn-danger" data-loading-text="<?php echo $this->tb['btn_loading']; ?>"><span class="glyphicon glyphicon-trash"></span> <?php echo $this->tb['btn_delete_page']; ?></button></div>
							</div>
						</div>
					</form>	
				</div>
			</div>
		</div>
		<?php } else { ?> 
		<div class="column content">
			<div class="inner">
				<div class="alert alert-danger"><h4><?php echo $this->tb['error_page_not_found']; ?><br /><br /><strong><?php echo Parse::queryKey('url');?></strong></h4></div>
			</div>
		</div>	
		<?php } ?>
		
<?php


$this->element('footer');


?>