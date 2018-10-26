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
 *	Copyright (c) 2014-2018 by Marc Anton Dahmen
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
	<div id="am-sidebar" class="am-sidebar uk-modal">
		<div class="am-sidebar-modal-dialog uk-modal-dialog uk-modal-dialog-blank">
			<div data-am-scroll-box='{"scrollToItem": ".uk-active"}'>
				<div data-am-tree>
					<div class="am-navbar-push uk-visible-large uk-margin-bottom">
						<a 
						href="<?php echo AM_BASE_INDEX . AM_PAGE_DASHBOARD; ?>"
						class="am-sidebar-logo"
						>
							<?php include AM_BASE_DIR . '/automad/gui/svg/logo.svg'; ?>
						</a>	
					</div>
					<div class="am-sidebar-search uk-visible-small uk-margin-bottom">
						<?php echo $this->Html->searchField(Text::get('search_placeholder')); ?>
					</div>
					<ul class="uk-nav uk-nav-side uk-margin-small-top">
						<li class="uk-nav-header">
							<?php Text::e('sidebar_header_global'); ?>
						</li>
						<li>
							<a href="<?php echo AM_BASE_INDEX . '/'; ?>">
								<i class="uk-icon-share uk-icon-justify"></i>&nbsp;
								<?php echo $this->sitename; ?>
							</a>
						</li>
						<li<?php if (!Core\Parse::query('context')) { echo ' class="uk-active"'; }?>>
							<a href="<?php echo AM_BASE_INDEX . AM_PAGE_DASHBOARD; ?>">
								<i class="uk-icon-desktop uk-icon-justify"></i>&nbsp;
								<?php Text::e('dashboard_title'); ?>
							</a>
						</li>
						<li<?php if (Core\Parse::query('context') == 'system_settings') { echo ' class="uk-active"'; }?>>
							<a href="?context=system_settings">
								<i class="uk-icon-sliders uk-icon-justify"></i>&nbsp;
								<?php Text::e('sys_title'); ?>
							</a>
						</li>
						<li<?php if (Core\Parse::query('context') == 'edit_shared') { echo ' class="uk-active"'; }?>>
							<a href="?context=edit_shared">
								<i class="uk-icon-globe uk-icon-justify"></i>&nbsp;
								<?php Text::e('shared_title'); ?>
							</a>
						</li>
						<li class="uk-nav-divider"></li>
					</ul>
					<?php 
					
						$header = 	Text::get('sidebar_header_pages') . 
							  		'&nbsp;&nbsp;&nbsp;<span class="uk-badge">' . 
							  		count($this->collection) . 
							  		'</span>';
									
						echo $this->Html->siteTree(
							'', 
							$this->collection, 
							array('context' => 'edit_page'), 
							false, 
							$header
						); 
					
					?> 
					<ul class="uk-nav uk-nav-side uk-hidden-large">
						<li class="uk-nav-divider"></li>
						<li>
							<a href="?context=logout">
								<i class="uk-icon-power-off"></i>&nbsp;
								<?php Text::e('btn_log_out'); ?>
								<i class="uk-icon-angle-double-left"></i>
								<?php echo ucwords(User::get()); ?>
								<i class="uk-icon-angle-double-right"></i>
							</a>
						</li>	
					</ul>
				</div>	
			</div>
		</div>	
	</div>	
	<?php } ?> 
			