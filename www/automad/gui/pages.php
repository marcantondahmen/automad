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
 *	AUTOMAD CMS
 *
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */

/**
 *	Add, edit, move or remove a page and its content.
 */


define('AUTOMAD', true);
require 'elements/base.php';


$S = new Core\Site(false);
$collection = $S->getCollection();


if (isset($_POST['url'])) {
	
	

	$page = $collection[$_POST['url']];
	$prefix = substr(basename($page->path), 0, strpos(basename($page->path), '.'));	
	$data = Core\Parse::textFile(AM_BASE_DIR . AM_DIR_PAGES . $page->path . $page->template . '.' . AM_FILE_EXT_DATA);

	
	$standardKeys = array(AM_KEY_TITLE, AM_KEY_TAGS, AM_KEY_THEME, AM_KEY_URL, AM_KEY_HIDDEN);
	
	
	foreach ($standardKeys as $key) {
		
		if (!isset($data[$key])) {
			$data[$key] = false;
		}
		
	}
	
	if (!$data[AM_KEY_TITLE]) {
		$data[AM_KEY_TITLE] = basename($_POST['url']);
	}
	
	
	// find templates of current site theme
	$siteThemeTemplates = array_filter(glob(AM_BASE_DIR . AM_DIR_THEMES . '/' . $G->siteData[AM_KEY_THEME] . '/*.php'), function($file) {
		return false === in_array(basename($file), array('error.php', 'results.php'));
	});
	
	// Find all templates
	$templates = array_filter(glob(AM_BASE_DIR . AM_DIR_THEMES . '/*/*.php'), function($file) {
		return false === in_array(basename($file), array('error.php', 'results.php'));
	});
		
	$G->guiTitle = 'Edit Page "' . $data[AM_KEY_TITLE] . '"';




	
} else {
	
	$G->guiTitle = false;
	$page = false;

}


$G->element('header-1200');


/*
echo '<pre>';
//print_r($page);
//print_r($siteThemeTemplates);
//print_r($templates);
print_r($_POST);
echo '</pre>';
*/


?>

<div class="box">
	<div id="tree" class="bg"><?php echo $G->siteTree('', $collection); ?></div>
</div>

<?php if ($page) { ?>

<div class="box big">
	<h2 class="text bg"><b><?php echo $page->url; ?></b></h2>
</div>	

<div class="box big">
	
	<div class="menu" id="page-menu">

		<?php if ($page->path != '/') { ?>
		
		<form class="item" id="delete" title="<?php echo $data[AM_KEY_TITLE]; ?>" method="post">
			<input type="hidden" name="url" value="<?php echo $page->url; ?>" />
			<input type="hidden" name="action" value="delete" />
			<input class="bg button" type="submit" value="Delete Page" />
		</form>

		<form class="item" id="move" method="post">
			<input type="hidden" name="url" value="<?php echo $page->url; ?>" />
			<input type="hidden" name="action" value="move" />
			<input type="hidden" name="parentUrl" value="" />
			<input class="bg button" type="submit" value="Move Page" />
		</form>
		
		<div id="move-tree" style="display: none;"><?php 
			// Re-Use the siteTree() method to get the tree of potential parent pages and change form submission action in JS.
			echo $G->siteTree('', $collection, true); 
		?></div>
	
		<?php } ?>

		<form class="item" method="post">
			<input type="hidden" name="url" value="<?php echo $page->url; ?>" />
			<input type="hidden" name="action" value="add" />
			<input class="bg button" type="submit" value="Add Sub-Page" />
		</form>
	
		<form class="item" method="post">
			<input class="bg button" type="button" value="Manage Files" />
		</form>
	
	</div>	
	
</div>	

<div class="box big">
	
	<form id="edit" class="item" method="post">
		
		<input type="hidden" name="url" value="<?php echo $page->url; ?>" />
		<input type="hidden" name="action" value="edit" />
		
		<div class="item">
			<div id="title">
				<label for="edit-data-title" class="bg input"><?php echo ucwords(AM_KEY_TITLE); ?></label>
				<input id="edit-data-title" class="bg input" type="text" name="edit[data][<?php echo AM_KEY_TITLE; ?>]" value="<?php echo $data[AM_KEY_TITLE]; ?>" />
			</div><div id="prefix">
				<label for="edit-prefix" class="bg input">Index</label>
				<input id="edit-prefix" class="bg input" type="text" name="edit[prefix]" value="<?php echo $prefix; ?>" placeholder="optional" />
			</div>
		</div>
		
		<div class="item">
			<label for="edit-data-tags" class="bg input"><?php echo ucwords(AM_KEY_TAGS); ?> (comma separated)</label>
			<input id="edit-data-tags" class="bg input" type="text" name="edit[data][<?php echo AM_KEY_TAGS; ?>]" value="<?php echo $data[AM_KEY_TAGS]; ?>" />
		</div>
		
		<?php
		foreach ($data as $key => $value) {
			// Only user defined vars
			// Standard vars are processed separately
			if (!in_array($key, $standardKeys)) {
				echo '<div class="custom item"><label for="edit-data-' . $key . '" class="bg input">' . ucwords($key) . '</label><textarea id="edit-data-' . $key . '" class="bg input" name="edit[data][' . $key . ']" rows="8">' . $value . '</textarea></div>';	
			}
		}
		?>
		
		<input id="edit-addCustom" class="item bg button" type="button" value="Add another variable" />
		
		<div class="item">
			<div id="template">
				<label for="edit-theme_template" class="bg input">Theme</label>
				<div class="selectbox bg input">
					<select id="edit-theme_template" name="edit[theme_template]" size="1">
					<?php		
					// List templates of current sitewide theme
					foreach($siteThemeTemplates as $template) {
			
						echo '<option';

						if (!$data[AM_KEY_THEME] && basename($template) === $page->template . '.php') {
							 echo ' selected';
						}
			
						echo ' value="' . basename($template) . '">' . ucwords(str_replace('.php', '', basename($template))) . ' (Theme from Site Settings)</option>';

					}
		
					// List all found template along with their theme folder
					foreach($templates as $template) {
			
						echo '<option';
		
						if ($data[AM_KEY_THEME] === basename(dirname($template)) && basename($template) === $page->template . '.php') {
							 echo ' selected';
						}
		
						echo ' value="' . basename(dirname($template)) . '/' . basename($template) . '">' . ucwords(basename(dirname($template))) . ' Theme > ' . ucwords(str_replace('.php', '', basename($template))) . '</option>';
					}
					?> 
					</select>   	
				</div>
			</div><div id="hidden">
				<label for="edit-hidden" class="bg input">Visibility</label>
				<div class="checkbox bg input"><input id="edit-hidden" type="checkbox" name="edit[<?php echo AM_KEY_HIDDEN; ?>]" />Hide page from navigation</div>
			</div>
		</div>
		
		<div class="item">
			<label for="edit-redirect" class="bg input">To redirect this page to another location instead of showing its content, just fill in any URL</label>
			<input id="edit-redirect" class="bg input" type="text" name="edit[data][<?php echo AM_KEY_URL; ?>]" value="<?php echo $data[AM_KEY_URL]; ?>" placeholder="optional" />
		</div>
		
		<div class="item">
			<input id="edit-save" class="bg button" type="submit" value="Save" />
		</div>
			
	</form>
	
	<form class="item" method="post">
		
		<div class="item">
			<input type="hidden" name="url" value="<?php echo $page->url; ?>" />
			<input type="submit" class="bg button" value="Discard Changes" />
		</div>
		
	</form>
	
</div>

<script>guiPages();</script>

<?php } else { ?>

<script>$('#tree').find('form').first().submit();</script>

<?php }


$G->element('footer');


?>