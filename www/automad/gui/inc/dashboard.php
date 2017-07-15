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
$Selection->sortPages(AM_KEY_MTIME . ' desc');
$latestPages = $Selection->getSelection(false, false, 0, 12);


$this->guiTitle = $this->guiTitle . ' / ' . Text::get('dashboard_title');
$this->element('header');


?>
		
		<div class="uk-block">
			<h1 class="uk-margin-large-top uk-margin-large-bottom">
				<?php echo $this->sitename; ?>
			</h1>
			<span class="uk-badge uk-visible-small uk-margin-bottom">
				<?php echo getenv('SERVER_NAME'); ?>
			</span>
		</div>
		<ul class="uk-grid uk-grid-width-1-2 uk-grid-width-medium-1-3 uk-margin-large-bottom">
			<li>
				<i class="uk-icon-heartbeat uk-icon-small uk-margin-bottom"></i>
				<div class="uk-text-small uk-text-bold uk-margin-small-bottom">
					<?php Text::e('dashboard_modified'); ?>
				</div>
				<?php echo date('j. M Y', $mTime); ?>
				<span class="uk-hidden-small">, <?php echo date('G:i', $mTime); ?> h</span>
				<div class="uk-margin-top">
					<a href="<?php echo AM_BASE_INDEX; ?>" class="uk-button uk-button-small uk-text-truncate">
						<span class="uk-hidden-small"><i class="uk-icon-share"></i>&nbsp;</span>
						<?php Text::e('btn_inpage_edit'); ?>
					</a>
				</div>
			</li>
			<li>
				<i class="uk-icon-code-fork uk-icon-small uk-margin-bottom"></i>
				<div class="uk-text-small uk-text-bold uk-margin-small-bottom">
					Automad Version
				</div>
				<?php echo AM_VERSION; ?>
				<div data-am-status="update">
					<button class="uk-button uk-button-small uk-margin-top" disabled>
						<i class="uk-icon-circle-o-notch uk-icon-spin"></i>&nbsp;
						<?php Text::e('btn_getting_data'); ?>
					</button>
				</div>
			</li>
			<li class="uk-position-relative uk-hidden-small">
				<i class="uk-icon-hdd-o uk-icon-small uk-margin-bottom"></i>
				<div class="uk-text-small uk-text-bold uk-margin-small-bottom">
					<?php Text::e('dashboard_server'); ?>
				</div>
				<span class="uk-text-truncate"><?php echo getenv('SERVER_NAME'); ?></span>
				<div class="uk-margin-top">
					<a href="#am-server-info-modal" class="uk-button uk-button-small" data-uk-modal>
						<i class="uk-icon-pie-chart"></i>&nbsp;
						<?php Text::e('dashboard_server_info'); ?>
					</a>
				</div>
			</li>
		</ul>
		<ul class="uk-grid uk-grid-width-medium-1-2">
			<li class="uk-margin-top" data-am-status="cache">
				<button class="uk-button uk-button-large uk-width-1-1" disabled>
					<i class="uk-icon-circle-o-notch uk-icon-spin"></i>&nbsp;
					<?php Text::e('btn_getting_data'); ?>
				</button>
			</li>
			<li class="uk-margin-top uk-margin-bottom" data-am-status="debug">
				<button class="uk-button uk-button-large uk-width-1-1" disabled>
					<i class="uk-icon-circle-o-notch uk-icon-spin"></i>&nbsp;
					<?php Text::e('btn_getting_data'); ?>
				</button>
			</li>
		</ul>
		<hr />
		<div class="uk-block uk-padding-bottom-remove">
			<h2><?php Text::e('dashboard_recently_edited'); ?></h2>
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