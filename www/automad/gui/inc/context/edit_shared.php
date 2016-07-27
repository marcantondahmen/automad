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
 *	Copyright (c) 2014-2016 by Marc Anton Dahmen
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
$this->element('title');


?>

		<div class="automad-navbar" data-uk-sticky>
			
			<?php $this->element('searchbar'); ?>
			
			<div class="automad-navbar-context uk-width-1-1">
				<ul class="uk-subnav">
					<li>
						<a href="?context=edit_shared" class="uk-text-truncate">
							<i class="uk-icon-globe uk-icon-justify"></i>&nbsp;&nbsp;<?php echo Text::get('shared_title'); ?>
						</a>
					</li>
				</ul>
			</div>	
			<!-- Menu -->
			<div class="uk-grid uk-grid-small">
				<!-- Content Switcher -->
				<div class="uk-width-2-3">
					<div class="uk-grid uk-grid-small" data-uk-switcher="{connect:'#automad-shared-content', toggle:'> div > button', animation: 'uk-animation-fade'}">
						<!-- Data -->
						<div class="uk-width-1-2">
							<button class="uk-button uk-width-1-1 uk-text-truncate" type="button">
								<i class="uk-icon-file-text-o"></i>
								<span class="uk-hidden-small">&nbsp;&nbsp;<?php echo Text::get('btn_data'); ?></span>
							</button>
						</div>
						<!-- Files -->
						<div class="uk-width-1-2">
							<button class="uk-button uk-width-1-1 uk-text-truncate" type="button">
								<i class="uk-icon-folder-open-o"></i>
								<span class="uk-hidden-small">&nbsp;&nbsp;<?php echo Text::get('btn_files'); ?></span>
							</button>
						</div>
					</div>	
				</div>
				<!-- Save -->
				<div class="uk-width-1-3">
					<button class="uk-button uk-button-success uk-width-1-1 uk-text-truncate" type="button" data-automad-submit="shared_data">
						<i class="uk-icon-save"></i><span class="uk-hidden-small">&nbsp;&nbsp;<?php echo Text::get('btn_save'); ?></span>
					</button>
				</div>
			</div>
			
		</div>
		
		<!-- Content -->
		<div class="uk-block uk-padding-bottom-remove">
			<ul id="automad-shared-content" class="uk-switcher">
				<!-- Data -->
			    	<li>
					<form class="uk-form uk-form-stacked" data-automad-init data-automad-handler="shared_data">
						<div class="uk-text-center">
							<i class="uk-icon-circle-o-notch uk-icon-spin uk-icon-small uk-margin-top"></i>
						</div>
					</form>
			    	</li>
				<!-- Files -->
				<li>
					<form class="uk-form uk-form-stacked" data-automad-init data-automad-handler="files" data-automad-confirm="<?php echo Text::get('confirm_delete_files'); ?>">
						<div class="uk-text-center">
							<i class="uk-icon-circle-o-notch uk-icon-spin uk-icon-small uk-text-muted uk-margin-top"></i>
						</div>
					</form>
				</li>
			</ul>
		</div>
		
<?php


$this->element('footer');


?>