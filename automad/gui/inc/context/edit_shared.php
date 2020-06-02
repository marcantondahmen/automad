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
 *	The GUI page to edit the global content. As part of the GUI, this file is only to be included via the GUI class.
 */


$this->guiTitle = $this->guiTitle . ' / ' . Text::get('shared_title');
$this->element('header');


?>
	
		<ul class="uk-subnav uk-subnav-pill uk-margin-top">
			<li class="uk-disabled uk-hidden-small"><i class="uk-icon-files-o"></i></li>
			<li><a href=""><?php Text::e('shared_title'); ?></a></li>
		</ul>
	
		<?php 
			echo Components\Nav\Switcher::render('#am-shared-content', array(
				array(
					'icon' => '<i class="uk-icon-file-text"></i>',
					'text' => Text::get('btn_data')
				),
				array(
					'icon' => '<span class="uk-badge am-badge-folder" data-am-count="[data-am-file-info]"></span>',
					'text' => Text::get('btn_files') . '&nbsp;&nbsp;<span class="uk-badge" data-am-count="[data-am-file-info]"></span>'
				)
			));
		?>
	
		<ul id="am-shared-content" class="uk-switcher">
			<!-- Data -->
			<li>
				<form 
				class="uk-form uk-form-stacked" 
				data-am-init 
				data-am-handler="shared_data"
				>
					<?php echo Components\Loading::render(); ?>
				</form>
			</li>
			<!-- Files -->
			<li>
				<form 
				class="uk-form uk-form-stacked" 
				data-am-init 
				data-am-handler="files" 
				data-am-confirm="<?php Text::e('confirm_delete_files'); ?>"
				>
					<?php echo Components\Loading::render(); ?>
				</form>
			</li>
		</ul>
		
		<!-- Select Image Modal -->
		<?php echo Components\Modal\SelectImage::render(); ?>

		<!-- Add Link Modal -->
		<?php echo Components\Modal\Link::render(); ?>

<?php


$this->element('footer');


?>