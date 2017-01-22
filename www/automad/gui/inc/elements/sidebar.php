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
 *	Copyright (c) 2014-2017 by Marc Anton Dahmen
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
	<div id="am-sidebar" class="uk-offcanvas">
		<div class="uk-offcanvas-bar am-navbar-push">
			<div data-am-scroll-box='{"scrollToItem": ".uk-active"}'>
				<div data-am-tree>
					<a href="#am-add-page-modal" class="uk-visible-small uk-margin-large-bottom uk-button uk-button-primary" data-uk-modal>
						<i class="uk-icon-plus"></i>&nbsp;
						<?php Text::e('btn_add_page'); ?>
					</a>
					<ul class="uk-nav uk-nav-side">
						<li>
							<a href="<?php echo AM_BASE_URL ?>" class="uk-text-truncate" target="_blank">
								<i class="uk-icon-share uk-icon-justify"></i>&nbsp;
								<?php echo $this->sitename; ?>
							</a>
						</li>
						<li<?php if (!Core\Parse::queryKey('context')) { echo ' class="uk-active"'; }?>>
							<a href="<?php echo AM_BASE_URL . AM_INDEX . AM_PAGE_GUI; ?>">
								<i class="uk-icon-th-large uk-icon-justify"></i>&nbsp;
								<?php Text::e('dashboard_title'); ?>
							</a>
						</li>
						<?php if (Core\Parse::queryKey('context') == 'search') { ?>
						<li class="uk-active">
							<a href="">
								<i class="uk-icon-search uk-icon-justify"></i>&nbsp;
								<?php Text::e('search_title'); ?>
							</a>
						</li>
						<?php } ?>
						<li class="uk-nav-divider"></li>
						<li class="uk-nav-header"><?php Text::e('sidebar_header_global'); ?></li>
						<li<?php if (Core\Parse::queryKey('context') == 'system_settings') { echo ' class="uk-active"'; }?>>
							<a href="?context=system_settings">
								<i class="uk-icon-cog uk-icon-justify"></i>&nbsp;
								<?php Text::e('sys_title'); ?>
							</a>
						</li>
						<li<?php if (Core\Parse::queryKey('context') == 'edit_shared') { echo ' class="uk-active"'; }?>>
							<a href="?context=edit_shared">
								<i class="uk-icon-globe uk-icon-justify"></i>&nbsp;
								<?php Text::e('shared_title'); ?>
							</a>
						</li>
						<li class="uk-nav-divider"></li>
					</ul>
					<?php 
					
						$header = Text::get('sidebar_header_pages') . '&nbsp;&nbsp;<span class="uk-badge uk-float-right">' . count($this->collection) . '</span>';
						echo $this->Html->siteTree('', $this->collection, array('context' => 'edit_page'), false, $header); 
					
					?> 
				</div>	
			</div>
		</div>	
	</div>	
	<?php } ?> 
			