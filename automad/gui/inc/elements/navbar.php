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


if (User::get()) {

	// Get form handler to be submitted. If no matching handler exists, set an empty string.
	$context = Core\Request::query('context');
	$handlers = array('edit_page' => 'page_data', 'edit_shared' => 'shared_data');

	if (isset($handlers[$context])) {
		$submit = $handlers[$context];
	} else {
		$submit = '';
	}
		
?>
	
	<nav class="am-navbar">
		<div class="am-navbar-nav">
			<!-- Logo -->
			<div class="am-navbar-logo">
				<a href="<?php echo AM_BASE_INDEX . AM_PAGE_DASHBOARD; ?>">
					<?php echo Components\Logo::render(); ?>
				</a>
			</div>
			<!-- Search -->
			<div class="am-navbar-search">
				<?php 
					echo Components\Form\Search::render(
						Text::get('search_placeholder') . ' ' . 
						htmlspecialchars($this->getShared()->get(AM_KEY_SITENAME)),
						'[Ctrl + ⇧ + Space]'
					);
				?>
			</div>
			<!-- Buttons -->
			<div class="am-navbar-buttons">
				<div class="am-icon-buttons">
					<!-- Debug Status -->
					<span data-am-status="debug"></span>
					<!-- Add Page -->
					<a 
					href="#am-add-page-modal" 
					class="uk-button uk-button-primary" 
					title="<?php Text::e('btn_add_page'); ?>"
					data-uk-modal
					data-uk-tooltip="{pos:'bottom-right'}"
					>
						<i class="uk-icon-plus"></i>
					</a>
					<!-- Save -->
					<?php if ($submit) { ?>
					<button 
					title="<?php Text::e('btn_save'); ?>[Ctrl + S]" 
					class="uk-button uk-button-success" 
					data-am-submit="<?php echo $submit; ?>" 
					data-uk-tooltip="{pos:'bottom-right'}" 
					disabled
					>
						<i class="uk-icon-check"></i>
					</button>
					<?php } ?>	
					<!-- More -->
					<div 
					class="uk-position-relative uk-visible-large" 
					data-uk-dropdown="{mode:'click'}"
					>
						<div class="uk-button">
							<i class="uk-icon-ellipsis-v"></i>
						</div>
						<div class="uk-dropdown uk-dropdown-small">
							<ul class="uk-nav uk-nav-dropdown">
								<li>
									<a href="?context=logout">
										<i class="uk-icon-power-off uk-icon-justify"></i>&nbsp;
										<?php echo Text::get('btn_log_out'); ?>
										<i class="uk-icon-angle-double-left"></i>
										<?php echo ucwords(User::get()) ?>
										<i class="uk-icon-angle-double-right"></i>
									</a>
								</li>
								<li>
									<a href="?context=system_settings#<?php echo Core\Str::sanitize(Text::get('sys_user'), true); ?>">
										<i class="uk-icon-user uk-icon-justify"></i>&nbsp;
										<?php Text::e('btn_manage_users'); ?>
									</a>
								</li>
								<li>
									<a href="#am-about-modal" data-uk-modal>
										<i class="uk-icon-lightbulb-o uk-icon-justify"></i>&nbsp;
										<?php Text::e('btn_about'); ?>
									</a>
								</li>
							</ul>
						</div>
					</div>
					<!-- Sidebar -->
					<a href="#am-sidebar" 
					class="uk-button uk-hidden-large" 
					data-uk-modal
					>
						<i class="uk-icon-navicon uk-icon-justify"></i>
					</a>
				</div>
			</div>
		</div>	
	</nav>
	
<?php 

} 

?>
