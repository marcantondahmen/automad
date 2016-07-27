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
 *	Copyright (c) 2016 by Marc Anton Dahmen
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





$Selection = new Core\Selection($this->Automad->getCollection());
$pagesCount = count($Selection->getSelection(false));
$Selection->sortPages(AM_KEY_MTIME, SORT_DESC);
$latestPages = $Selection->getSelection(false, 0, 10);


$this->guiTitle = $this->guiTitle . ' / ' . Text::get('dashboard_title');
$this->element('header');


?>

		<div class="uk-block uk-block-muted uk-margin-top">
			<h2><?php echo $this->sitename; ?></h2>
			<h1><?php echo Text::get('dashboard_welcome') . ' ' . ucwords(User::get()); ?></h1>
		</div>
		<div class="automad-navbar" data-uk-sticky>
			<?php $this->element('searchbar'); ?>
		</div>
		<div class="uk-block uk-block-muted uk-margin-small-top">
			<ul class="uk-grid uk-grid-width-1-1 uk-grid-width-medium-1-2 uk-grid-width-xlarge-1-4" data-uk-grid-margin>
				<li><i class="uk-icon-hdd-o"></i>&nbsp;&nbsp;<span data-automad-status="cache"></span></li>
				<li><i class="uk-icon-bug"></i>&nbsp;&nbsp;<span data-automad-status="debug"></span></li>
				<li><i class="uk-icon-files-o"></i>&nbsp;&nbsp;<?php echo Text::get('sidebar_header_pages'); ?>&nbsp;&nbsp;<span class="uk-badge uk-badge-notification"><?php echo $pagesCount; ?></span></li>
				<li><span data-automad-status="users"></span></li>
			</ul>
		</div>
		<div class="uk-block">
			<a href="#automad-add-page-modal" class="uk-button uk-button-large uk-width-1-1" data-uk-modal>
				<i class="uk-icon-plus"></i>&nbsp;&nbsp;<?php echo Text::get('btn_add_page'); ?>
			</a>
			<div class="uk-panel uk-panel-box uk-margin-small-top uk-margin-small-bottom">
				<i class="uk-icon-clock-o"></i>&nbsp;&nbsp;<?php echo Text::get('dashboard_recently_edited'); ?>
			</div>
			<?php echo $this->Html->pageGrid($latestPages); ?>		
		</div>
	
<?php


$this->element('footer');


?>