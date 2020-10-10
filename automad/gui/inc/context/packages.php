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
 *	Copyright (c) 2019-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;
use Automad\System as System;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	The package manager.
 */


$this->guiTitle = $this->guiTitle . ' / ' . Text::get('packages_title');
$this->element('header');


?>

		<ul class="uk-subnav uk-subnav-pill uk-margin-top">
			<li class="uk-disabled uk-hidden-small"><i class="uk-icon-download"></i></li>
			<li><a href=""><?php Text::e('packages_title'); ?></a></li>
		</ul>
		
		<!-- Filters -->
		<div class="am-sticky">
			<div class="uk-form">
				<input 
				class="uk-width-1-1" 
				type="search" 
				name="filter" 
				placeholder="<?php Text::e('packages_filter'); ?>"
				data-am-packages-filter 
				/>
			</div>
		</div>	
		
		<!-- Packages -->
		<div class="uk-margin-large-top" data-am-packages>
			<i class="uk-icon-circle-o-notch uk-icon-spin uk-icon-small"></i>
		</div>
		
		<!-- Modal windows -->
		<?php 
		
		$progressModals = array(
			'am-modal-update-all-packages-progress' => array(
				'icon' => 'uk-icon-refresh uk-icon-spin',
				'text' => Text::get('packages_updating_all')
			),
			'am-modal-update-package-progress' => array(
				'icon' => 'uk-icon-refresh uk-icon-spin',
				'text' => Text::get('packages_updating')
			),	
			'am-modal-remove-package-progress' => array(
				'icon' => 'uk-icon-close',
				'text' => Text::get('packages_removing')
			),
			'am-modal-install-package-progress' => array(
				'icon' => 'uk-icon-download',
				'text' => Text::get('packages_installing')
			)	
		);
	
		foreach ($progressModals as $id => $content) {
				
		?>

			<div id="<?php echo $id; ?>" class="uk-modal">
				<div class="uk-modal-dialog uk-padding-remove">
					<div class="am-progress-panel uk-progress uk-progress-striped uk-active">
						<div class="uk-progress-bar uk-margin-remove" style="width: 100%;">			
							<i class="<?php echo $content['icon']; ?>"></i>&nbsp;
							<?php echo $content['text']; ?>
						</div>
					</div>
				</div>
			</div>

		<?php 
		
		} 


$this->element('footer');


?>