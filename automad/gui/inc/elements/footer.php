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


defined('AUTOMAD') or die('Direct access not permitted!');


?>
		
		<?php if (User::get()) { ?>

			<!-- Footer -->
			<div class="am-footer uk-position-bottom">
				<a 
				href="#am-about-modal" 
				class="uk-button uk-button-mini uk-text-muted" 
				data-uk-modal
				>
					Automad &mdash; Version <?php echo AM_VERSION; ?>
				</a>	
			</div>

			<!-- About Modal -->
			<?php echo Components\Modal\About::render('am-about-modal'); ?>

			<!-- Add Page Modal -->
			<div id="am-add-page-modal" class="uk-modal">
				<div class="uk-modal-dialog">
					<div class="uk-modal-header">
						<?php Text::e('btn_add_page'); ?>
						<a href="#" class="uk-modal-close uk-close"></a>
					</div>
					<form class="uk-form uk-form-stacked" data-am-handler="add_page">
						<input id="am-add-page-input" type="hidden" name="url" value="" />
						<div class="uk-form-row">
							<label for="am-add-page-modal-input-title" class="uk-form-label uk-margin-top-remove">Title</label>
							<input 
							id="am-add-page-modal-input-title" 
							class="uk-form-controls uk-form-large uk-width-1-1" 
							type="text" 
							name="subpage[title]" 
							value="" 
							required 
							/>
						</div>
						<?php echo Components\Form\CheckboxPrivate::render('subpage[private]'); ?>
						<hr>
						<?php if (!AM_HEADLESS_ENABLED) { ?>
							<div class="uk-form-row">
								<label class="uk-form-label uk-margin-top-remove"><?php Text::e('page_theme_template'); ?></label>
								<?php 
									echo Components\Form\SelectTemplate::render(
										$this->getAutomad(),
										$this->getThemelist(),
										'subpage[theme_template]'
									); 
								?>
							</div>
						<?php } ?>
					</form>
					<div class="uk-form-stacked uk-margin-top">
						<label class="uk-form-label uk-margin-top-remove">
							<?php Text::e('page_add_location'); ?>
						</label>
						<div data-am-tree="#am-add-page-input">
							<?php echo Components\Nav\SiteTree::render($this->getAutomad(), '', array(), false, false); ?>
						</div>
					</div>
					<div class="uk-modal-footer uk-text-right">
						<button type="button" class="uk-modal-close uk-button">
							<i class="uk-icon-close"></i>&nbsp;&nbsp;<?php Text::e('btn_close'); ?>
						</button>
						<button type="button" class="uk-button uk-button-success" data-am-submit="add_page">
							<i class="uk-icon-plus"></i>&nbsp;&nbsp;<?php Text::e('btn_add_page'); ?>
						</button>
					</div>
				</div>
			</div>
		
		<?php } ?>

	</div> <!-- .uk-container -->	
	
	<!-- No-JS -->
	<div id="am-no-js" class="uk-animation-fade">
		<div class="uk-container uk-container-center uk-margin-large-top">
			<div class="uk-container-center uk-width-medium-1-2">
				<div class="uk-alert uk-alert-danger">
					<?php Text::e('error_no_js'); ?>
				</div>
			</div>
		</div>
	</div>
	
</body>
</html>