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


if (User::get()) {

	// Get form handler to be submitted. If no matching handler exists, set an empty string.
	$context = Core\Parse::query('context');
	$handlers = array('edit_page' => 'page_data', 'edit_shared' => 'shared_data');

	if (isset($handlers[$context])) {
		$submit = $handlers[$context];
	} else {
		$submit = '';
	}
	
	$searchPlaceholder = Text::get('search_placeholder') . ' ' . htmlspecialchars($this->sitename);
		
?>
	
	<nav class="am-navbar">
		<ul class="am-navbar-nav">
			<!-- Logo -->
			<li class="am-navbar-logo">
				<a href="<?php echo AM_BASE_INDEX . AM_PAGE_GUI; ?>"><i class="uk-icon-automad"></i></a>
			</li>
			<!-- Search -->
			<li class="am-navbar-search">
				<form class="uk-form uk-width-1-1" action="" method="get" data-am-autocomplete-submit>
					<input type="hidden" name="context" value="search">	
					<div class="uk-autocomplete uk-width-1-1" data-uk-autocomplete="{source: Automad.autocomplete.data, minLength: 2}">
						<div class="uk-form-icon uk-width-1-1" title="Ctrl + Space" data-uk-tooltip>
							<i class="uk-icon-search"></i>
							<input class="uk-form-controls uk-form-large uk-width-1-1" title="" name="query" type="text" placeholder="<?php echo $searchPlaceholder; ?>" required>
						</div>
					</div>
				</form>
			</li>
			<!-- Add Page -->
			<li class="uk-hidden-small">
				<a href="#am-add-page-modal" class="uk-button uk-button-danger" data-uk-modal>
					<i class="uk-icon-plus"></i>&nbsp;&nbsp;<?php Text::e('btn_add_page'); ?>
				</a>
			</li>
			<!-- Save -->
			<?php if ($submit) { ?>
			<li>
				<button title="Cmd/Ctrl + S" class="uk-button uk-button-success" data-am-submit="<?php echo $submit; ?>" data-uk-tooltip disabled>
					<span class="uk-hidden-small"><i class="uk-icon-check"></i>&nbsp;&nbsp;</span><?php Text::e('btn_save'); ?>
				</button>
			</li>
			<?php } ?>
			<!-- Search modal for small screens -->
			<li class="uk-visible-small">
				<a href="#am-search-modal" class="am-navbar-icon am-navbar-icon-danger" data-uk-modal>
					<i class="uk-icon-search"></i>
				</a>
			</li>
			<!-- User -->
			<li class="uk-visible-large">
				<div class="uk-position-relative" data-uk-dropdown="{mode:'click',pos:'bottom-right'}">
					<div class="am-navbar-icon">
						<i class="uk-icon-user"></i>
					</div>
					<div class="uk-dropdown uk-dropdown-small">
						<ul class="uk-nav uk-nav-dropdown">
							<li>
								<a href="?context=logout">
									<i class="uk-icon-sign-out"></i>&nbsp;
									<?php echo Text::get('btn_log_out'); ?>
									<i class="uk-icon-angle-double-left"></i>
									<?php echo ucwords(User::get()) ?>
									<i class="uk-icon-angle-double-right"></i>
								</a>
							</li>
							<li>
								<a href="?context=system_settings#<?php echo Core\Str::sanitize(Text::get('sys_user'), true); ?>">
									<i class="uk-icon-users"></i>&nbsp;
									<?php Text::e('btn_manage_users'); ?>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</li>
			<!-- Sidebar -->
			<li class="uk-hidden-large">
				<a href="#" class="am-navbar-icon" data-am-toggle-sidebar="#am-sidebar">
					<i class="uk-icon-navicon uk-icon-justify"></i>
				</a>
			</li>
		</ul>
	</nav>
	
	<!-- Search modal for small screens -->
	<div id="am-search-modal" class="uk-modal">
		<div class="uk-modal-dialog">
			<form class="uk-form" action="" method="get" data-am-autocomplete-submit>
				<input type="hidden" name="context" value="search">	
				<div class="uk-autocomplete uk-width-1-1" data-uk-autocomplete="{source: Automad.autocomplete.data, minLength: 2}">
					<div class="uk-form-icon uk-width-1-1">
						<i class="uk-icon-search"></i>
						<input class="uk-form-controls uk-form-large uk-width-1-1" name="query" type="text" placeholder="<?php echo $searchPlaceholder; ?>" required>
					</div>	
				</div>
			</form>
			<button type="button" class="uk-modal-close uk-button uk-button-primary uk-margin-top uk-width-1-1">
				<i class="uk-icon-close"></i>&nbsp;
				<?php Text::e('btn_close'); ?>
			</button>
		</div>
	</div>
	
<?php 

} 

?>
