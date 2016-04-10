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
 *	The GUI page to edit the global content. As part of the GUI, this file is only to be included via the GUI class.
 */


$this->guiTitle = $this->guiTitle . ' / ' . Text::get('shared_title');
$this->element('header');


?>

		<div class="column subnav">
			<div class="scroll">
				<div class="inner">
					<!-- Nav tabs -->
					<ul class="nav nav-pills nav-stacked">
						<li class="active">
							<a href="#data" data-toggle="tab">
								<span class="glyphicon glyphicon-align-left"></span><span class="hidden-md"> <?php echo Text::get('btn_data'); ?></span>
							</a>
						</li>
						<li>
							<a href="#files" data-toggle="tab">
								<span class="glyphicon glyphicon-folder-open"></span><span class="hidden-md"> <?php echo Text::get('btn_files'); ?></span>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		
		<div class="column content">
			<div class="inner">
				<div class="alert alert-info">
					<h3><?php echo Text::get('shared_title'); ?></h3>
				</div>
				<!-- Tab panes -->
				<div class="tab-content">
					<div id="data" class="tab-pane fade in active">
						<form class="automad-form automad-init" data-automad-handler="shared_data" role="form"><span class="glyphicon glyphicon-time"></span> <?php echo Text::get('btn_loading'); ?></form>
					</div>
					<div id="files" class="tab-pane fade">
						<form class="automad-form automad-init" data-automad-handler="files" role="form"></form>
					</div>
				</div>
			</div>
		</div>	

<?php


$this->element('footer');


?>