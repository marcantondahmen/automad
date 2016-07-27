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
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


?>
	
	<?php if (User::get()) { ?> 
	<div id="automad-sidebar" class="uk-offcanvas">
		
		<div class="uk-offcanvas-bar">
			<div data-automad-scroll-box='{"scrollToItem": ".uk-active"}'>
				<div class="uk-margin-top uk-margin-bottom" data-automad-tree>
					
					<i class="uk-icon-automad uk-icon-small"></i>
					
					<ul class="uk-nav uk-margin-top">
						<li<?php if (!Core\Parse::queryKey('context')) { echo ' class="uk-active"'; } ?>>
							<a href="<?php echo AM_BASE_URL . AM_INDEX . AM_PAGE_GUI; ?>"><i class="uk-icon-th-large uk-icon-justify"></i>&nbsp;&nbsp;<?php echo Text::get('dashboard_title'); ?></a>
						</li>
						<li class="uk-nav-divider"></li>
						<li>
							<a href="#automad-add-page-modal" data-uk-modal>
								<i class="uk-icon-plus-circle uk-icon-justify"></i>&nbsp;&nbsp;<?php echo Text::get('btn_add_page'); ?>
							</a>	
						</li>
						<li class="uk-nav-divider"></li>
						<li class="uk-nav-header">
							<?php echo Text::get('sidebar_header_site'); ?>
						</li>
						<li<?php if (Core\Parse::queryKey('context') == 'system_settings') { echo ' class="uk-active"'; }?>>
							<a href="?context=system_settings"><i class="uk-icon-cog uk-icon-justify"></i>&nbsp;&nbsp;<?php echo Text::get('sys_title'); ?></a>
						</li>			
						<li<?php if (Core\Parse::queryKey('context') == 'edit_shared') { echo ' class="uk-active"'; }?>>
							<a href="?context=edit_shared"><i class="uk-icon-globe uk-icon-justify"></i>&nbsp;&nbsp;<?php echo Text::get('shared_title'); ?></a>
						</li>
						<li class="uk-nav-divider"></li>
					</ul>
					
					<?php echo $this->Html->siteTree('', $this->collection, array('context' => 'edit_page'), false, Text::get('sidebar_header_pages')); ?>
					
					<ul class="uk-nav">
						<li class="uk-nav-divider"></li>
						<li>
							<a href="http://automad.org" target="_blank"><i class="uk-icon-book uk-icon-justify"></i>&nbsp;&nbsp;<?php echo Text::get('btn_docs'); ?></a>
						</li>
						<li class="uk-nav-divider"></li>
						<li>
							<a href="?context=logout"><i class="uk-icon-power-off uk-icon-justify"></i>&nbsp;&nbsp;<?php echo ucfirst(strtolower(Text::get('btn_log_out'))) . ' <b>' . User::get() . '</b>'; ?></a>
						</li>
					</ul>
					
				</div>
			
			</div>
		</div>	
		
		<!-- Sitetree Add Page Modal -->
		<div id="automad-add-page-modal" class="uk-modal">
			<div class="uk-modal-dialog">
				<div class="uk-modal-header">
					<?php echo Text::get('btn_add_page'); ?>
				</div>
				<form class="uk-form" data-automad-handler="add_page">
					<input id="automad-add-page-input" type="hidden" name="url" value="" />
					<div class="uk-margin-small-bottom">
						<input class="uk-form-controls uk-form-large uk-width-1-1" type="text" name="subpage[title]" value="" placeholder="Title" required />
					</div>
					<?php echo $this->Html->templateSelectBox('subpage[theme_template]'); ?>
				</form>
				<div class="uk-margin-top uk-panel uk-panel-box" data-automad-tree="#automad-add-page-input">
					<?php echo $this->Html->siteTree('', $this->collection, array(), true, Text::get('page_add_destination')); ?>
				</div>
				<div class="uk-modal-footer uk-text-right">
					<button type="button" class="uk-modal-close uk-button">
						<i class="uk-icon-close"></i>&nbsp;&nbsp;<?php echo Text::get('btn_close'); ?>
					</button>
					<button type="button" class="uk-button uk-button-primary" data-automad-submit="add_page">
						<i class="uk-icon-plus"></i>&nbsp;&nbsp;<?php echo Text::get('btn_add_page'); ?>
					</button>
				</div>
			</div>
		</div>
		
	</div>	
	<?php } ?> 
			