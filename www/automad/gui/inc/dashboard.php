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
 *	Copyright (c) 2016-2017 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	The GUI Start Page. As part of the GUI, this file is only to be included via the GUI class.
 */


$Cache = new Core\Cache();
$mTime = $Cache->getSiteMTime();
$Selection = new Core\Selection($this->Automad->getCollection());
$Selection->sortPages(AM_KEY_MTIME, SORT_DESC);
$latestPages = $Selection->getSelection(false, 0, 12);


$this->guiTitle = $this->guiTitle . ' / ' . Text::get('dashboard_title');
$this->element('header');


?>
		
		<div class="uk-block uk-padding-bottom-remove">
			<h1 class="uk-margin-large-top uk-margin-small-bottom"><?php echo $this->sitename; ?></h1>
		</div>
		<div class="uk-panel uk-panel-box uk-panel-box-primary">
			<ul class="uk-grid uk-grid-width-1-2 uk-grid-width-medium-1-3">
				<li>
					<i class="uk-icon-heartbeat uk-icon-small uk-margin-bottom"></i>
					<div class="uk-text-small uk-text-bold uk-margin-small-bottom"><?php Text::e('dashboard_modified'); ?></div>
					<?php echo date('j. M Y', $mTime); ?><span class="uk-hidden-small">, <?php echo date('G:i', $mTime); ?> h</span>
				</li>
				<li>
					<i class="uk-icon-code-fork uk-icon-small uk-margin-bottom"></i>
					<div class="uk-text-small uk-text-bold uk-margin-small-bottom">Automad Version</div>
					<?php echo AM_VERSION; ?>
				</li>
				<li class="uk-position-relative uk-hidden-small">
					<i class="uk-icon-hdd-o uk-icon-small uk-margin-bottom"></i>
					<a href="#am-server-info-modal" class="uk-button uk-button-primary uk-button-mini uk-float-right" data-uk-modal>
						<i class="uk-icon-plus-circle"></i>&nbsp;
						<?php Text::e('btn_more'); ?>
					</a>
					<div class="uk-text-small uk-text-bold uk-margin-small-bottom">
						<?php Text::e('dashboard_server'); ?>
					</div>
					<span class="uk-text-truncate"><?php echo getenv('SERVER_NAME'); ?></span>
				</li>
			</ul>
		</div>
		<ul class="uk-grid uk-grid-width-medium-1-2">
			<li class="uk-margin-small-bottom" data-am-status="cache"></li>
			<li class="uk-margin-large-bottom" data-am-status="debug"></li>
		</ul>
		<hr class="uk-margin-top-remove" />
		<a href="<?php echo AM_BASE_INDEX; ?>" class="uk-button uk-button-danger uk-button-large uk-width-1-1">
			<i class="uk-icon-share"></i>&nbsp;
			<?php Text::e('btn_inpage_edit'); ?>
		</a>
		<div class="uk-block uk-padding-bottom-remove">
			<h2 class="uk-margin-top"><?php Text::e('dashboard_recently_edited'); ?></h2>
			<?php echo $this->Html->pageGrid($latestPages); ?>
		</div>

		<!-- Server Info Modal -->
		<div id="am-server-info-modal" class="uk-modal">
			<div class="uk-modal-dialog">
				<div class="uk-modal-header">
					<?php Text::e('dashboard_server_info'); ?>
					<a class="uk-modal-close uk-close"></a>
				</div>
				<div class="uk-margin-bottom">
					Operating System:<br />
					<?php echo php_uname('s') . ' / ' . php_uname('r'); ?>
				</div>
				<div class="uk-margin-bottom">
					Server Software:<br />
					<?php echo getenv('SERVER_SOFTWARE'); ?>
				</div>
				<div class="uk-margin-large-bottom">
					PHP:<br /> 
					<?php echo phpversion(); ?> / <?php echo php_sapi_name(); ?>
				</div>
				<span class="uk-badge uk-badge-notification">
					<?php echo Text::get('dashboard_memory') . ' ' . (memory_get_peak_usage(true) / 1048576) . 'M  (' . ini_get('memory_limit') . ')'; ?>
				</span>
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