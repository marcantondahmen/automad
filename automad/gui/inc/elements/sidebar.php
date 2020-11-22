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
							<?php echo Components\Logo::render(); ?>
						</a>	
					</div>
					<div class="am-sidebar-search uk-visible-small uk-margin-bottom">
						<?php echo Components\Form\Search::render(Text::get('search_placeholder')); ?>
					</div>
					<ul class="uk-nav uk-nav-side uk-margin-small-top">
						<li class="uk-nav-header">
							<?php Text::e('sidebar_header_global'); ?>
						</li>
						<?php if (!AM_HEADLESS_ENABLED) { ?>
							<li>
								<a href="<?php echo AM_BASE_INDEX . '/'; ?>">
									<i class="uk-icon-bookmark-o uk-icon-justify"></i>&nbsp;
									<?php echo $this->getShared()->get(AM_KEY_SITENAME); ?>
								</a>
							</li>
						<?php } ?>
						<li<?php if (!Core\Request::query('context')) { echo ' class="uk-active"'; }?>>
							<a href="<?php echo AM_BASE_INDEX . AM_PAGE_DASHBOARD; ?>">
								<i class="uk-icon-tv uk-icon-justify"></i>&nbsp;
								<?php Text::e('dashboard_title'); ?>
							</a>
						</li>
						<li<?php if (Core\Request::query('context') == 'system_settings') { echo ' class="uk-active"'; }?>>
							<a href="?context=system_settings">
								<i class="uk-icon-sliders uk-icon-justify"></i>&nbsp;
								<?php Text::e('sys_title'); ?>&nbsp;
								<?php echo Components\Status\Span::render('update_badge'); ?>
							</a>
						</li>
						<li<?php if (Core\Request::query('context') == 'edit_shared') { echo ' class="uk-active"'; }?>>
							<a href="?context=edit_shared">
								<i class="uk-icon-files-o uk-icon-justify"></i>&nbsp;
								<?php Text::e('shared_title'); ?>
							</a>
						</li>
						<li<?php if (Core\Request::query('context') == 'packages') { echo ' class="uk-active"'; }?>>
							<a href="?context=packages">
								<i class="uk-icon-download uk-icon-justify"></i>&nbsp;
								<?php Text::e('packages_title'); ?>&nbsp;
								<?php echo Components\Status\Span::render('outdated_packages'); ?>
							</a>
						</li>
						<li class="uk-nav-divider"></li>
					</ul>
					<?php 
					
						$header = 	Text::get('sidebar_header_pages') . 
							  		'&nbsp;&mdash;&nbsp;' . 
							  		count($this->getAutomad()->getCollection());
									
						echo Components\Nav\SiteTree::render(
							$this->getAutomad(),
							'',  
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
			