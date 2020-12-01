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


/*
 *	The GUI page to edit page content. As part of the GUI, this file is only to be included from the Gui class.
 */


$url = Core\Request::query('url');


if ($Page = $this->getAutomad()->getPage($url)) {
	$this->guiTitle = $this->guiTitle . ' / ' . htmlspecialchars($Page->get(AM_KEY_TITLE));
}


$this->element('header');


?>
		
		<?php if ($Page) { 
			
			echo Components\Nav\Breadcrumbs::render($this->getAutomad());
		
			$items = array(
				array(
					'icon' => '<i class="uk-icon-file-text"></i>',
					'text' => Text::get('btn_data')
				),
				array(
					'icon' => '<span class="uk-badge am-badge-folder" data-am-count="[data-am-file-info]"></span>',
					'text' => Text::get('btn_files') . '&nbsp;&nbsp;<span class="uk-badge" data-am-count="[data-am-file-info]"></span>'
				)
			);
			
			$dropdown = array();
			
			if ($url != '/') {
				$dropdown = array(
					// Edit data inpage.
					'<a href="' . AM_BASE_INDEX . $url . '">' . 
						'<i class="uk-icon-pencil uk-icon-justify"></i>&nbsp;&nbsp;' . 
						Text::get('btn_inpage_edit') . 
					'</a>', 
					// Duplicate Page.
					'<a href="#" data-am-submit="duplicate_page">' . 
						'<i class="uk-icon-clone uk-icon-justify"></i>&nbsp;&nbsp;' . 
						Text::get('btn_duplicate_page') . 
					'</a>' . 
					'<form data-am-handler="duplicate_page" data-am-url="' . $url . '"></form>',
					// Move Page.
					'<a href="#am-move-page-modal" data-uk-modal>' .
						'<i class="uk-icon-arrows uk-icon-justify"></i>&nbsp;&nbsp;' . 
						Text::get('btn_move_page') . 
					'</a>',
					// Delete Page.
					'<a href="#" data-am-submit="delete_page">' .
						'<i class="uk-icon-remove uk-icon-justify"></i>&nbsp;&nbsp;' . 
						Text::get('btn_delete_page') . 
					'</a>' .
					'<form data-am-handler="delete_page" data-am-url="' . $url . '" data-am-confirm="' . Text::get('confirm_delete_page') . '">' .
					'<input type="hidden" name="title" value="' . htmlspecialchars($Page->get(AM_KEY_TITLE)) . '" />' .
					'</form>',
					// Copy page URL to clipboard.
					'<a href="#" data-am-clipboard="' . $url . '">' .
						'<i class="uk-icon-link uk-icon-justify"></i>&nbsp;&nbsp;' . 
						Text::get('btn_copy_url_clipboard') . 
					'</a>'
				);
			}
		
			echo Components\Nav\Switcher::render('#am-page-content', $items, $dropdown, $Page->private);
			
		?>
	
		<ul id="am-page-content" class="uk-switcher">
			<!-- Data -->
		    <li>
				<form 
				class="uk-form uk-form-stacked" 
				data-am-init 
				data-am-handler="page_data" 
				data-am-url="<?php echo $url; ?>"
				data-am-path="<?php echo $Page->get(AM_KEY_PATH); ?>"
				>
					<?php echo Components\Loading::render(); ?>
				</form>
		    </li>
			<!-- Files -->
			<li>
				<form 
				class="uk-form uk-form-stacked" 
				data-am-init 
				data-am-handler="files" 
				data-am-url="<?php echo $url; ?>" 
				data-am-confirm="<?php Text::e('confirm_delete_files'); ?>"
				>
					<?php echo Components\Loading::render(); ?>
				</form>
			</li>
		</ul>
		
		<!-- Select Image Modal -->
		<?php echo Components\Modal\SelectImage::render($url); ?>

		<!-- Add Link Modal -->
		<?php echo Components\Modal\Link::render(); ?>

		<!-- Move Page Modal -->
		<div id="am-move-page-modal" class="uk-modal">
			<div class="uk-modal-dialog">
				<div class="uk-modal-header">
					<?php Text::e('btn_move_page'); ?>
					<a href="#" class="uk-modal-close uk-close"></a>
				</div>
				<div class="uk-form-stacked">
					<label class="uk-form-label uk-margin-top-remove">
						<?php Text::e('page_move_destination'); ?>
					</label>
					<div data-am-tree="#am-move-page-input">
						<?php echo Components\Nav\SiteTree::render($this->getAutomad(), '', array(), true, false); ?>
					</div>
				</div>
				<form data-am-handler="move_page" data-am-url="<?php echo $url; ?>">
					<input 
					type="hidden" 
					name="title" 
					value="<?php echo htmlspecialchars($Page->get(AM_KEY_TITLE)); ?>" 
					/>
					<input id="am-move-page-input" type="hidden" name="destination" value="" />
				</form>
				<div class="uk-modal-footer uk-text-right">
					<button type="button" class="uk-modal-close uk-button">
						<i class="uk-icon-close"></i>&nbsp;
						<?php Text::e('btn_close'); ?>
					</button>
					<button type="button" class="uk-button uk-button-success" data-am-submit="move_page">
						<i class="uk-icon-arrows"></i>&nbsp;
						<?php Text::e('btn_move_page'); ?>
					</button>
				</div>
			</div>
		</div>
		
		<?php } else { ?>
		
		<div class="uk-alert uk-alert-danger uk-margin-large-top">
			<?php Text::e('error_page_not_found'); ?><br />
			"<?php echo $url; ?>"
		</div>
			
		<?php } ?>
		
<?php


$this->element('footer');


?>