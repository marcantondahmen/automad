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


defined('AUTOMAD') or die('Direct access not permitted!');


?>
		
		<!-- Footer -->
		<div 
		class="am-footer uk-position-bottom<?php if (!User::get()) { ?> uk-text-center<?php } ?>"
		>
			<a 
			href="https://automad.org" 
			class="uk-text-small" 
			target="_blank"
			>
				Automad <?php echo AM_VERSION; ?>
			</a>	
		</div>
		
	</div> <!-- .uk-container -->	
	
	<?php if (User::get()) { ?>
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
				<div class="uk-form-row">
					<label class="uk-form-label"><?php Text::e('page_theme_template'); ?></label>
					<?php 
						echo $this->Html->selectTemplate(
							$this->Themelist,
							'subpage[theme_template]'
						); 
					?>
				</div>
			</form>
			<div class="uk-form-stacked">
				<label class="uk-form-label">
					<?php Text::e('page_add_location'); ?>
				</label>
				<div data-am-tree="#am-add-page-input">
					<?php echo $this->Html->siteTree('', $this->collection, array(), false, false); ?>
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