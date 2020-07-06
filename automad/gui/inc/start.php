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
 *	Copyright (c) 2016-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	The Dashboard Start Page. As part of the dashboard, this file is only to be included via the Dashboard class.
 */


$Cache = new Core\Cache();
$mTime = $Cache->getSiteMTime();
$Selection = new Core\Selection($this->getAutomad()->getCollection());
$Selection->sortPages(AM_KEY_MTIME . ' desc');
$latestPages = $Selection->getSelection(false, false, 0, 12);


$this->guiTitle = $this->guiTitle . ' / ' . Text::get('dashboard_title');
$this->element('header');


?>
		
		<div class="uk-margin-large-top">
			<h1><?php echo $this->getShared()->get(AM_KEY_SITENAME); ?></h1>
		</div>
		<div class="uk-margin-top uk-margin-large-bottom">
			<a 
			href="#am-server-info-modal" 
			class="uk-button uk-button-small uk-button-link uk-text-truncate" 
			data-uk-modal
			>
				<i class="uk-icon-hdd-o uk-icon-justify"></i>&nbsp;
				<?php echo getenv('SERVER_NAME'); ?>
			</a>
			<br>
			<span class="uk-text-small">
				<i class="uk-icon-heartbeat uk-icon-justify"></i>&nbsp;
				<span class="uk-hidden-small"><?php Text::e('dashboard_modified'); ?></span>
				<?php echo date('j. M Y, G:i', $mTime); ?>h
			</span>
		</div>
		<?php if (!AM_HEADLESS_ENABLED) { ?>
			<p>
				<a href="<?php echo AM_BASE_INDEX . '/'; ?>" class="uk-button uk-button-primary uk-button-large uk-width-1-1 uk-text-left-small">
					<i class="uk-icon-pencil"></i>&nbsp;
					<?php Text::e('btn_inpage_edit'); ?>
				</a>
			</p>
		<?php } ?>
		<ul class="uk-grid uk-grid-width-medium-1-3 uk-margin-small-top">
			<?php if (AM_HEADLESS_ENABLED) { ?>
				<li class="uk-margin-small-bottom">
					<a 
					href="?context=system_settings#<?php echo Core\Str::sanitize(Text::get('sys_headless')); ?>"
					class="uk-button uk-button-success uk-button-large uk-text-truncate uk-width-1-1 uk-text-left"
					>
						<i class="uk-icon-toggle-on uk-icon-justify"></i>&nbsp;
						<?php Text::e('sys_headless_enable'); ?>
					</a>
				</li>
			<?php } ?>
			<li class="uk-margin-small-bottom">
				<?php echo Components\Status\Button::render('cache', Core\Str::sanitize(Text::get('sys_cache'))); ?>
			</li>
			<?php if (!AM_HEADLESS_ENABLED) { ?>
				<li class="uk-margin-small-bottom">
					<?php echo Components\Status\Button::render('debug', Core\Str::sanitize(Text::get('sys_debug'))); ?>
				</li>
			<?php } ?>
			<li class="uk-margin-small-bottom">
				<?php echo Components\Status\Button::render('update', Core\Str::sanitize(Text::get('sys_update'))); ?>
			</li>
		</ul>
		<div class="uk-margin-large-top">
			<h2><?php Text::e('dashboard_recently_edited'); ?></h2>
			<?php echo Components\Grid\Pages::render($latestPages); ?>
		</div>

		<!-- Server Info Modal -->
		<div id="am-server-info-modal" class="uk-modal">
			<div class="uk-modal-dialog">
				<div class="uk-modal-header">
					<?php echo getenv('SERVER_NAME'); ?>
					<a class="uk-modal-close uk-close"></a>
				</div>
				<p>
					Automad Version:<br /> 
					<?php echo AM_VERSION; ?>
				</p>
				<p>
					Operating System:<br />
					<?php echo php_uname('s') . ' / ' . php_uname('r'); ?>
				</p>
				<p>
					Server Software:<br />
					<?php echo getenv('SERVER_SOFTWARE'); ?>
				</p>
				<p>
					PHP:<br /> 
					<?php echo phpversion(); ?> / <?php echo php_sapi_name(); ?>
				</p>
				<hr>
				<div class="uk-alert uk-margin-top-remove">
					<?php echo Text::get('dashboard_memory') . ' ' . (memory_get_peak_usage(true) / 1048576) . 'M  (' . ini_get('memory_limit') . ')'; ?>
				</div>
				<div class="uk-modal-footer uk-text-right">
					<button type="button" class="uk-modal-close uk-button">
						<i class="uk-icon-close"></i>&nbsp;&nbsp;<?php Text::e('btn_close'); ?>
					</button>
				</div>
			</div>
		</div>
	
<?php


$this->element('footer');


?>