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
$Selection = new Core\Selection($this->Automad->getCollection());
$Selection->sortPages(AM_KEY_MTIME, SORT_DESC);
$latestPages = $Selection->getSelection(false, 0, 12);


$this->guiTitle = $this->guiTitle . ' / ' . Text::get('dashboard_title');
$this->element('header');


?>

		<div class="uk-block uk-padding-bottom-remove uk-margin-top">
			<h1 class="uk-margin-small-top uk-margin-bottom-remove"><?php echo $this->sitename; ?></h1>
			<h2 class="uk-margin-top"><?php Text::e('dashboard_title'); ?></h2>
		</div>
		<div class="uk-block uk-padding-bottom-remove">
			<div class="uk-panel uk-panel-box">
				<ul class="uk-grid uk-grid-width-small-1-3" data-uk-grid-margin>
					<li>
						<i class="uk-icon-heartbeat uk-icon-small uk-margin-bottom uk-margin-small-top"></i>
						<div class="uk-text-small"><?php Text::e('dashboard_modified'); ?></div>
						<?php echo date('j. M Y, G:i', $Cache->getSiteMTime()); ?> h
					</li>
					<li>
						<i class="uk-icon-code-fork uk-icon-small uk-margin-bottom uk-margin-small-top"></i>
						<div class="uk-text-small">Automad Version</div>
						<?php echo AM_VERSION; ?>
					</li>
					<li class="uk-position-relative">
						<i class="uk-icon-hdd-o uk-icon-small uk-margin-bottom uk-margin-small-top"></i>
						<a href="#" class="uk-button uk-button-mini uk-button-primary uk-float-right" data-uk-toggle="{target:'#am-server-info', animation:'uk-animation-fade'}">
							<?php Text::e('btn_more'); ?>
						</a>
						<div class="uk-text-small">
							<?php Text::e('dashboard_server'); ?>
							
						</div>
						<?php echo getenv('SERVER_NAME'); ?>
					</li>
				</ul>
			</div>
			<div id="am-server-info" class="uk-hidden uk-panel uk-panel-box uk-panel-box-primary">
				<div class="uk-text-truncate" title="<?php echo htmlspecialchars(getenv('SERVER_SOFTWARE')); ?>">
					<?php echo getenv('SERVER_SOFTWARE'); ?>
				</div>
				<div class="uk-text-truncate" title="<?php echo htmlspecialchars(php_uname('v')); ?>">
					<?php echo php_uname('v'); ?>
				</div>
				PHP <?php echo phpversion(); ?> / <?php echo php_sapi_name(); ?>
				<br />
				<?php echo Text::get('dashboard_memory') . ' ' . (memory_get_peak_usage(true) / 1048576) . 'M  (' . (ini_get('memory_limit')) . ')'; ?>
			</div>
			<ul class="uk-grid uk-grid-width-medium-1-2">
				<li class="uk-margin-large-bottom" data-am-status="cache"></li>
				<li class="uk-margin-large-bottom" data-am-status="debug"></li>
			</ul>
		</div>
		<div class="uk-block">
			<h3><?php Text::e('dashboard_recently_edited'); ?></h3>			
			<?php echo $this->Html->pageGrid($latestPages); ?>
		</div>
		
<?php


$this->element('footer');


?>