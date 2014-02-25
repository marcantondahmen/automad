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

			<div class="col-md-4">
				<?php $this->element('navigation');?> 
			</div>
			
			<div class="col-md-8">
					
				<?php if ($P) { ?>
			
				<div class="list-group">
			
					<a class="list-group-item" href="<?php echo AM_BASE_URL . $P->url; ?>" target="_blank"><h4><?php echo $P->url; ?></h4></a>
					
					<div class="list-group-item clearfix">
						
						<div class="pull-right">
							<div class="btn-group">
								<a class="btn btn-default" href="<?php echo AM_BASE_URL . $P->url; ?>" target="_blank"><span class="glyphicon glyphicon-eye-open"></span> Visit Page</a>
								<button type="button" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span> Add Subpage</button>
								<?php if ($P->path != '/') { ?> 
								<button type="button" class="btn btn-default"><span class="glyphicon glyphicon-arrow-right"></span> Move Page</button>
								<button type="button" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span> Delete Page</button>
								<?php } ?> 
							</div>
						</div>
						
					</div>
						
					<div class="list-group-item">
					
						<!-- Nav tabs -->
						<ul class="nav nav-tabs">
							<li class="active"><a href="#data" data-toggle="tab"><span class="glyphicon glyphicon-align-left"></span> Data &amp; Settings</a></li>
							<li><a href="#files" data-toggle="tab"><span class="glyphicon glyphicon-picture"></span> Files</a></li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content">
							<div id="data" class="tab-pane active">
								<form class="row automad-form" data-automad-ajax-handler="page_data" role="form">
									<input type="hidden" name="url" value="<?php echo $P->url; ?>" />
									<div class="col-md-12 text-muted"><strong>Getting page data ...</strong></div>
								</form>
							</div>
							<div id="files" class="tab-pane">
								
								
							</div>
						</div>
				
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