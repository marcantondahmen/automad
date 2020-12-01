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
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	The GUI Sytem Settings' Page. As part of the GUI, this file is only to be included via the GUI class.
 */


$this->guiTitle = $this->guiTitle . ' / ' . Text::get('sys_title');
$this->element('header');


?>
	
		<ul class="uk-subnav uk-subnav-pill uk-margin-top">
			<li class="uk-disabled uk-hidden-small"><i class="uk-icon-sliders"></i></li>
			<li><a href=""><?php Text::e('sys_title'); ?></a></li>
		</ul>
		<?php
		 
			echo Components\Nav\Switcher::render('#am-sys-content', array(
				array(
					'icon' => '<i class="uk-icon-rocket"></i>',
					'text' => Text::get('sys_cache')
				),
				array(
					'icon' => '<i class="uk-icon-user"></i>',
					'text' => Text::get('sys_user')
				),
				array(
					'icon' => '<i class="uk-icon-refresh"></i>',
					'text' => Text::get('sys_update')
				),
				array(
					'icon' => '<i class="uk-icon-flag"></i>',
					'text' => Text::get('sys_language')
				),
				array(
					'icon' => '<span class="am-icon-headless"></span>',
					'text' => Text::get('sys_headless')
				),
				array(
					'icon' => '<i class="uk-icon-bug"></i>',
					'text' => Text::get('sys_debug')
				)
				
			), array(
				'<a href="#am-edit-config-modal" data-uk-modal>' .
					'<i class="uk-icon-file-text-o uk-icon-justify"></i>&nbsp;&nbsp;' . 
					Text::get('sys_config') . 
				'</a>'
			)); 
			
			echo Components\Modal\EditConfig::render('am-edit-config-modal');

		?> 

		<ul id="am-sys-content" class="uk-switcher">
			<!-- Cache -->
			<li>
				<?php echo Components\System\Cache::render(); ?>
			</li>
			<!-- User -->
			<li>
				<?php echo Components\System\Users::render(); ?>
			</li>
			<!-- Update -->
			<li>
				<?php echo Components\System\Update::render(); ?>
			</li>
			<!-- Language -->
			<li>
				<?php echo Components\System\Language::render(); ?>
			</li>
			<!-- Headless --> 
			<li>
				<?php echo Components\System\Headless::render(); ?>
			</li>
			<!-- Debug -->
			<li>
				<?php echo Components\System\Debug::render(); ?>
			</li>
		</ul>

<?php


$this->element('footer');


?>