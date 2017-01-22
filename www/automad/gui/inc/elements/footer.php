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


defined('AUTOMAD') or die('Direct access not permitted!');


?>
		
		<!-- Footer -->
		<div class="am-footer uk-position-bottom">
			<?php if (User::get()) { ?>
			<!-- <hr class="uk-margin-remove" /> -->
			<ul class="uk-grid uk-grid-width-1-1 uk-grid-width-medium-1-2">
				<li class="uk-margin-top">
					<a href="http://automad.org" class="uk-text-muted" target="_blank">
						<i class="uk-icon-automad"></i>&nbsp;&nbsp;
						<span class="uk-text-small"><?php echo AM_VERSION; ?></span>
					</a>
				</li>
				<li class="uk-margin-top uk-text-muted uk-text-right uk-text-left-small">
					<i class="uk-icon-user"></i>&nbsp;&nbsp;
					<?php echo User::get(); ?>&nbsp;&nbsp;
					<a href="?context=logout" class="uk-float-right uk-icon-button uk-icon-power-off" title="<?php Text::e('btn_log_out'); ?>"></a>
				</li>
			</ul>
			<?php } else { ?>
				<div class="uk-text-center uk-text-muted uk-margin-top">
					<i class="uk-icon-automad"></i>
				</div>
			<?php } ?>
		</div>
		
	</div> <!-- .uk-container -->	
	
	<?php if (User::get()) { ?>
	<!-- Add Page Modal -->
	<div id="am-add-page-modal" class="uk-modal">
		<div class="uk-modal-dialog">
			<div class="uk-modal-header">
				<?php Text::e('btn_add_page'); ?>
			</div>
			<form class="uk-form uk-form-stacked" data-am-handler="add_page">
				<input id="am-add-page-input" type="hidden" name="url" value="" />
				<div class="uk-form-row">
					<label for="am-add-page-modal-input-title" class="uk-form-label">Title</label>
					<input id="am-add-page-modal-input-title" class="uk-form-controls uk-form-large uk-width-1-1" type="text" name="subpage[title]" value="" required />
				</div>
				<div class="uk-form-row">
					<label class="uk-form-label"><?php Text::e('page_theme_template'); ?></label>
					<?php echo $this->Html->templateSelectBox('subpage[theme_template]'); ?>
				</div>
			</form>
			<hr />
			<div class="uk-badge uk-badge-notification">
				<i class="uk-icon-mouse-pointer"></i>&nbsp;
				<?php Text::e('page_add_location'); ?>
			</div>
			<div class="uk-margin-top uk-margin-large-bottom" data-am-tree="#am-add-page-input">
				<?php echo $this->Html->siteTree('', $this->collection, array(), false, false); ?>
			</div>
			<div class="uk-modal-footer uk-text-right">
				<button type="button" class="uk-modal-close uk-button">
					<i class="uk-icon-close"></i>&nbsp;&nbsp;<?php Text::e('btn_close'); ?>
				</button>
				<button type="button" class="uk-button uk-button-primary" data-am-submit="add_page">
					<i class="uk-icon-plus"></i>&nbsp;&nbsp;<?php Text::e('btn_add_page'); ?>
				</button>
			</div>
		</div>
	</div>
	<?php } ?>
	
	<!-- No-JS -->
	<div id="am-no-js" class="uk-block uk-animation-fade">
		<div class="uk-container uk-container-center uk-margin-large-top">
			<i class="uk-icon-warning uk-icon-large"></i>
			<?php Text::e('error_no_js'); ?>
		</div>
	</div>
	
</body>
</html>