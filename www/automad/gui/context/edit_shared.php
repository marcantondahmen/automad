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
 *	The GUI page to edit the global content. As part of the GUI, this file is only to be included via the GUI class.
 */


$this->guiTitle = $this->guiTitle . ' / Global Content &amp; Settings';
$this->element('header');


?>

		<div class="row">

			<div class="col-md-4">
				<?php $this->element('navigation');?>
			</div>
			
			<div class="col-md-8">
				
				<div class="list-group">
				
					<a class="list-group-item" href=""><h4><?php echo AM_DIR_SHARED; ?></h4></a>
				
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
						<form class="clearfix automad-form" data-automad-handler="shared_data" data-automad-url="" role="form"></form>
					</div>
					<div id="files" class="tab-pane fade">
						<script src="<?php echo AM_BASE_URL; ?>/automad/lib/jquery-file-upload/jquery.ui.widget.js" type="text/javascript" charset="utf-8"></script>
						<script src="<?php echo AM_BASE_URL; ?>/automad/lib/jquery-file-upload/jquery.fileupload.js" type="text/javascript" charset="utf-8"></script>
						<script src="<?php echo AM_BASE_URL; ?>/automad/lib/jquery-file-upload/jquery.iframe-transport.js" type="text/javascript" charset="utf-8"></script>
						<form class="clearfix automad-form" data-automad-handler="files" data-automad-url="" role="form"></form>
					</div>
				</div>
			
			</div>	
	
		</div>

<?php


$this->element('footer');


?>