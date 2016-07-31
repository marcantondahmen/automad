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


/*
 *	The GUI page to edit page content. As part of the GUI, this file is only to be included from the Gui class.
 */


if (array_key_exists(Core\Parse::queryKey('url'), $this->collection)) {
	
	$url = Core\Parse::queryKey('url');
	$Page = $this->collection[$url];
	$data = Core\Parse::textFile($this->Content->getPageFilePath($Page));

	if (!isset($data[AM_KEY_TITLE]) || !$data[AM_KEY_TITLE]) {
		$data[AM_KEY_TITLE] = basename($url);
	}

	$this->guiTitle = $this->guiTitle . ' / ' . $data[AM_KEY_TITLE];

} else {
	
	$Page = false;

}


$this->element('header');
$this->element('title');


?>

		<div class="automad-navbar" data-uk-sticky="{showup:true,animation:'uk-animation-slide-top'}">
			
			<?php $this->element('searchbar'); ?>
			
			<?php if ($Page) { ?>
			<div class="automad-navbar-context uk-width-1-1">
				<?php echo $this->Html->breadcrumbs(); ?>
			</div>	
			<!-- Menu -->
			<div class="uk-grid uk-grid-small">
				<!-- Content Switcher -->
				<div class="uk-width-2-5">
					<div class="uk-grid uk-grid-small" data-uk-switcher="{connect:'#automad-page-content', toggle:'> div > button', animation: 'uk-animation-fade'}">
						<!-- Data -->
						<div class="uk-width-1-2">
							<button class="uk-button uk-width-1-1 uk-text-truncate" type="button">
								<i class="uk-icon-file-text-o"></i>
								<span class="uk-hidden-small">&nbsp;&nbsp;<?php echo Text::get('btn_data'); ?></span>
							</button>
						</div>
						<!-- Files -->
						<div class="uk-width-1-2">
							<button class="uk-button uk-width-1-1 uk-text-truncate" type="button">
								<i class="uk-icon-folder-open-o"></i>
								<span class="uk-hidden-small">
									&nbsp;&nbsp;<?php echo Text::get('btn_files'); ?>&nbsp;&nbsp;
									<span class="uk-badge uk-badge-notification" data-automad-count=".automad-files-info"></span>
								</span>
							</button>
						</div>
					</div>
				</div>
				<!-- Add Subpage -->
				<div class="uk-width-1-5">
					<a class="uk-button uk-width-1-1 uk-text-truncate" href="#automad-add-subpage-modal" data-uk-modal>
						<i class="uk-icon-plus"></i>
						<span class="uk-hidden-small">&nbsp;&nbsp;<?php echo Text::get('btn_add_page'); ?></span>
					</a>
					<!-- Add Subpage Modal -->
					<div id="automad-add-subpage-modal" class="uk-modal">
						<div class="uk-modal-dialog">
							<form class="uk-form" data-automad-handler="add_subpage" data-automad-url="<?php echo $url; ?>">
								<div class="uk-modal-header">
									<?php echo Text::get('btn_add_page'); ?>
								</div>
								<div class="uk-margin-small-bottom">
									<input class="uk-form-controls uk-form-large uk-width-1-1" type="text" name="subpage[title]" value="" placeholder="Title" required data-automad-enter="#automad-add-subpage-submit" />
								</div>
								<?php echo $this->Html->templateSelectBox('subpage[theme_template]'); ?>
								<div class="uk-modal-footer uk-text-right">
									<button type="button" class="uk-modal-close uk-button">
										<i class="uk-icon-close"></i>&nbsp;&nbsp;<?php echo Text::get('btn_close'); ?>
									</button>
									<button id="automad-add-subpage-submit" type="submit" class="uk-button uk-button-primary">
										<i class="uk-icon-plus"></i>&nbsp;&nbsp;<?php echo Text::get('btn_add'); ?>
									</button>
								</div>
							</form>
						</div>
					</div>
				</div>
				<!-- Dropdown -->
				<div class="uk-width-1-5 uk-button-dropdown" data-uk-dropdown="{pos:'bottom-center',mode:'click'}">	
					<button class="uk-button uk-width-1-1" type="button" <?php if ($url == '/') { echo 'disabled'; } ?>>
						<span class="uk-hidden-small">
							<i class="uk-icon-clone"></i>&nbsp;&nbsp;
							<i class="uk-icon-arrows"></i>&nbsp;&nbsp;
							<i class="uk-icon-trash"></i>
						</span>
						<i class="uk-icon-ellipsis-h uk-visible-small"></i>
					</button>
					<div class="uk-dropdown uk-dropdown-close">
						<ul class="uk-nav uk-nav-dropdown">
							<!-- Duplicate Page -->
							<li>
								<a href="#" data-automad-submit="duplicate_page">
									<i class="uk-icon-clone"></i>&nbsp;&nbsp;<?php echo Text::get('btn_duplicate_page'); ?>
								</a>
								<form data-automad-handler="duplicate_page" data-automad-url="<?php echo $url; ?>"></form>
							</li>
							<!-- Move Page -->
							<li>
								<a href="#automad-move-page-modal" data-uk-modal>
									<i class="uk-icon-arrows"></i>&nbsp;&nbsp;<?php echo Text::get('btn_move_page'); ?>
								</a>
							</li>
							<!-- Delete Page -->
							<li>
								<a href="#" data-automad-submit="delete_page">
									<i class="uk-icon-trash"></i>&nbsp;&nbsp;<?php echo Text::get('btn_delete_page'); ?>
								</a>
								<form data-automad-handler="delete_page" data-automad-url="<?php echo $url; ?>" data-automad-confirm="<?php echo Text::get('confirm_delete_page'); ?>">
									<input type="hidden" name="title" value="<?php echo $data[AM_KEY_TITLE]; ?>" />
								</form>
							</li>
							
						</ul>
					</div>
				</div>
				<!-- Save -->
				<div class="uk-width-1-5">
					<button class="uk-button uk-button-success uk-width-1-1 uk-text-truncate" type="button" data-automad-submit="page_data">
						<i class="uk-icon-save"></i><span class="uk-hidden-small">&nbsp;&nbsp;<?php echo Text::get('btn_save'); ?></span>
					</button>
				</div>
			</div>
			<?php } ?>
			
		</div>
		
		<?php if ($Page) { ?>
					
		<div class="uk-block">
			<a href="<?php echo AM_BASE_URL . $Page->url; ?>" class="uk-text-truncate uk-text-left uk-button uk-text-muted uk-width-1-1" target="_blank">
				<i class="uk-icon-share"></i>&nbsp;&nbsp;<span class="uk-hidden-small"><?php echo AM_BASE_URL; ?></span><?php echo $Page->url; ?>
			</a>
		</div>

		<!-- Content -->
		<ul id="automad-page-content" class="uk-switcher">
			<!-- Data -->
		    	<li>
				<form class="uk-form uk-form-stacked" data-automad-init data-automad-handler="page_data" data-automad-url="<?php echo $url; ?>">
					<div class="uk-text-center">
						<i class="uk-icon-circle-o-notch uk-icon-spin uk-icon-small uk-margin-top"></i>
					</div>
				</form>
		    	</li>
			<!-- Files -->
			<li>
				<form class="uk-form uk-form-stacked" data-automad-init data-automad-handler="files" data-automad-url="<?php echo $url; ?>" data-automad-confirm="<?php echo Text::get('confirm_delete_files'); ?>">
					<div class="uk-text-center">
						<i class="uk-icon-circle-o-notch uk-icon-spin uk-icon-small uk-text-muted uk-margin-top"></i>
					</div>
				</form>
			</li>
		</ul>
		
		<!-- Move Page Modal -->
		<div id="automad-move-page-modal" class="uk-modal">
			<div class="uk-modal-dialog">
				<div class="uk-modal-header">
					<?php echo Text::get('btn_move_page'); ?>
				</div>
				<div class="uk-panel uk-panel-box" data-automad-tree="#automad-move-page-input">
					<?php echo $this->Html->siteTree('', $this->collection, array(), true, Text::get('page_move_destination')); ?>
				</div>
				<form data-automad-handler="move_page" data-automad-url="<?php echo $url; ?>">
					<input type="hidden" name="title" value="<?php echo $Page->get(AM_KEY_TITLE); ?>" />
					<input id="automad-move-page-input" type="hidden" name="destination" value="" />
				</form>
				<div class="uk-modal-footer uk-text-right">
					<button type="button" class="uk-modal-close uk-button">
						<i class="uk-icon-close"></i>&nbsp;&nbsp;<?php echo Text::get('btn_close'); ?>
					</button>
					<button type="button" class="uk-button uk-button-primary" data-automad-submit="move_page">
						<i class="uk-icon-arrows"></i>&nbsp;&nbsp;<?php echo Text::get('btn_move_page'); ?>
					</button>
				</div>
			</div>
		</div>
		
		<?php } else { ?>
		
		<div class="uk-block">
			<div class="uk-alert uk-alert-danger">
				<?php echo Text::get('error_page_not_found'); ?><br /><strong><?php echo Core\Parse::queryKey('url'); ?></strong>
			</div>
		</div>
				
			
			
		<?php } ?>
		
<?php


$this->element('footer');


?>