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




// Set URL
if (isset($_POST['url'])) {
	// If URL gets posted, use that one
	$url = $_POST['url'];
} else {
	// Else set URL to be the home page
	$url = '/';
}




// Get the Site, without parsing the txt files (speed)
$S = new Core\Site(false);
$collection = $S->getCollection();

// Get the page object (without the data), to determine the path to the data file.
$page = $collection[$url];




// If the user edited, moved or deleted the page...
if (isset($_POST['action'])) {
	
	
	if ($_POST['action'] == 'add') {
		
		
		$title = $_POST['add']['title'];
		$theme_template = $_POST['add']['theme_template'];
		
		// Get theme name.
		if (dirname($theme_template) != '.') {
			$theme = dirname($theme_template);
		} else {
			$theme = '';
		}
		
		// Build initial content for data file.
		$content = AM_KEY_TITLE . AM_PARSE_PAIR_SEPARATOR . ' ' . $title . "\r\n\r\n" . AM_PARSE_BLOCK_SEPARATOR . "\r\n\r\n" . AM_KEY_THEME . AM_PARSE_PAIR_SEPARATOR . ' ' . $theme;
		
		// Save new subpage		
		$subdir = str_replace('.', '_', Core\Parse::sanitize($title)) . '/';
		$newPagePath = $page->path . $subdir;
		
		$i = 1;
		
		// In case page exists already...
		while (file_exists(AM_BASE_DIR . AM_DIR_PAGES . $newPagePath)) {
			$newPagePath = $page->path . $i . '.' . $subdir;		
			$i++;	
		}
		
		$pageFile = AM_BASE_DIR . AM_DIR_PAGES . $newPagePath . str_replace('.php', '', basename($theme_template)) . '.' . AM_FILE_EXT_DATA;
		
		$old = umask(0);
		
		if (!file_exists(AM_BASE_DIR . AM_DIR_PAGES . $newPagePath)) {
			mkdir(AM_BASE_DIR . AM_DIR_PAGES . $newPagePath, 0777, true);
		}
		
		file_put_contents($pageFile, $content);
		
		umask($old);
		
		
	}
	
	
	// If the page got edited, update the txt file and rename/move the directory according to a possibly changed name or prefix.	
	if ($_POST['action'] == 'edit') {
			
			
		$edit = $_POST['edit'];
		$data = array_filter($edit['data']);
		
		
		// Set hidden parameter within the $data array. Since it is a checkbox, it gets passed separately.
		if (isset($edit['hidden'])) {
			$data['hidden'] = 1;
		}
		
	
		// The theme and the template get passed as theme/template.php combination separate form $edit['data']. 
		// That information has to be parsed first and "subdivided".
		
		// Get correct theme name.
		// If the theme is not set and there is no slash passed within 'theme_template', the resulting dirname is just a dot.
		// In that case, $data['theme'] gets removed (no theme - use site theme). 
		if (dirname($edit['theme_template']) != '.') {
			$data['theme'] = dirname($edit['theme_template']);
		} else {
			unset($data['theme']);
		}
					
		// Build file content to be written to the txt file.
		$pairs = array();
		
		foreach ($data as $key => $value) {
			$pairs[] = $key . AM_PARSE_PAIR_SEPARATOR . ' ' . $value;
		}
		
		$content = implode("\r\n\r\n" . AM_PARSE_BLOCK_SEPARATOR . "\r\n\r\n", $pairs);
		
		
		// Delete old file, in case, the template has changed.
		unlink($G->pageFile($page));
		
		
		// Build the path of the data file by appending the basename of 'theme_template' to $page->path.
		$newPageFile = AM_BASE_DIR . AM_DIR_PAGES . $page->path . str_replace('.php', '', basename($edit['theme_template'])) . '.' . AM_FILE_EXT_DATA;
		
		
		// Save new file within current directory, even when the prefix/title changed. 
		// Renaming/moving is done in a later step, to keep files and subpages bundled to the current text file.
		$old = umask(0);
		file_put_contents($newPageFile, $content);
		umask($old);
		
		
		// If the page is not the homepage, 
		// rename the page's directory including all children and all files, after saving according to title and prefix.
		// movePage() will check if renaming is needed, and will skip moving, when old and new path are equal.
		if ($url != '/') {
			
			if (!isset($edit['prefix'])) {
				$prefix = '';
			} else {
				$prefix = $edit['prefix'];
			}
		
			$newPagePath = $G->movePage($page->path, dirname($page->path), $prefix, $edit['data']['title']);
			
		} 
	
		
	}
	
	
	// Move page to a new location.
	if ($_POST['action'] == 'move') {
		
		$move = $_POST['move'];
		
		if (isset($move['parentUrl']) && $move['parentUrl']) {
				
			// Get new path by the posted parentUrl.
			foreach ($collection as $parentUrl => $parentPage) {
				
				if ($parentUrl == $move['parentUrl']) {
					
					$newParentPath = $parentPage->path;					
					break;
					
				}
				
			}
			
			// Move page directory
			$newPagePath = $G->movePage($page->path, $newParentPath, $G->extractPrefixFromPath($page->path), $move['title']);
			
		}
		

	}
	
	
	// Move a page to the trash
	if ($_POST['action'] == 'delete' && $url != '/') {
		
		if (isset($_POST['delete']['title']) && $_POST['delete']['title']) {
			
			$G->movePage($page->path, '..' . AM_DIR_TRASH . dirname($page->path), $G->extractPrefixFromPath($page->path), $_POST['delete']['title']);
			
		}
		
	}
	
	
	
	
	// After all the actions, 
	// the Site object has to be rebuilt, the cache has to be cleared and the filename for the page's text file has to be updated.
	
	// Clear the cache to make sure, the changes get reflected on the website directly.
	$C = new Core\Cache();
	$C->clear();
	
		
	// Rebuild Site object, since the file structure might be different now.
	$S = new Core\Site(false);
	$collection = $S->getCollection();
	

	// Find the page again and get the correctly determined URL (Site::collectPages()).
	// Therefore the page needs to be found by path ($newPagePath).
	if (isset($newPagePath)) {
		
		// If $newPagePath got defined before.
		foreach ($collection as $u => $p) {
			
			if ($p->path == $newPagePath) {
				$page = $p;
				$url = $u;
				break;
			}
			
		}
		
	} else {
		
		// If $newPagePath is not set, just make the homepage current.
		$url = '/';
		$page = $collection[$url];
		
	}
	
	
} 




// Load/Reload all data from the text file of the current page.
$data = Core\Parse::textFile($G->pageFile($page));




// These keys are always part of the edit-form and have to be normalized/created.
$standardKeys = array(AM_KEY_TITLE, AM_KEY_TAGS, AM_KEY_THEME, AM_KEY_URL, AM_KEY_HIDDEN);

foreach ($standardKeys as $key) {
	if (!isset($data[$key])) {
		$data[$key] = false;
	}
}

// Set title, in case the variable is not set (when editing the text file in an editor and the title wasn't set correctly)
if (!$data[AM_KEY_TITLE]) {
	$data[AM_KEY_TITLE] = basename($url);
}




$G->guiTitle = 'Edit Page "' . $data[AM_KEY_TITLE] . '"';
$G->element('header-1200');


?>


<div class="box">
	<div id="tree" class="bg"><?php echo $G->siteTree('', $collection, $url); ?></div>
</div>

<div class="box big">
	<h2 class="text bg"><b><?php echo $page->url; ?></b></h2>
</div>	

<div class="box big">

	<div class="menu" id="page-menu">

		<?php if ($page->path != '/') { ?>
	
		<form class="item" id="delete" method="post">
			<input type="hidden" name="url" value="<?php echo $page->url; ?>" />
			<input type="hidden" name="action" value="delete" />
			<input type="hidden" name="delete[title]" value="<?php echo $data[AM_KEY_TITLE]; ?>" />
			<input class="bg button" type="submit" value="Delete Page" />
		</form>

		<form class="item" id="move" method="post">
			<input type="hidden" name="url" value="<?php echo $page->url; ?>" />
			<input type="hidden" name="action" value="move" />
			<input type="hidden" name="move[parentUrl]" value="" />
			<input type="hidden" name="move[title]" value="<?php echo $data[AM_KEY_TITLE]; ?>" />
			<input class="bg button" type="submit" value="Move Page" />
		</form>
	
		<div id="move-tree" style="display: none;"><?php 
			// Re-Use the siteTree() method to get the tree of potential parent pages and change form submission action in JS.
			echo $G->siteTree('', $collection, $url, true); 
		?></div>

		<?php } ?>

		<form class="item" id="add" method="post">
			<input type="hidden" name="url" value="<?php echo $page->url; ?>" />
			<input type="hidden" name="action" value="add" />
			<input type="hidden" name="add[title]" value="" />
			<input type="hidden" name="add[theme_template]" value="" />
			<input class="bg button" type="submit" value="Add Subpage" />
		</form>
		
		<form id="add-dialog" style="display: none;" onkeypress="return event.keyCode != 13;">
			<input class="item bg input" type="text" name="title" value="" placeholder="Title">
			<div class="item"><?php echo $G->templateSelectBox('add-dialog-select', 'theme_template'); ?></div>
		</form>

		<form class="item" id="files">
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
				<input id="edit-data-title" class="bg input" type="text" name="edit[data][<?php echo AM_KEY_TITLE; ?>]" value="<?php echo $data[AM_KEY_TITLE]; ?>" onkeypress="return event.keyCode != 13;" />
			</div><div id="prefix">
				<label for="edit-prefix" class="bg input">Index</label>
				<input id="edit-prefix" class="bg input" type="text" name="edit[prefix]" value="<?php echo $G->extractPrefixFromPath($page->path); ?>" <?php if ($page->path == '/') { echo 'disabled'; } else { echo 'placeholder="optional"'; } ?> onkeypress="return event.keyCode != 13;" />
			</div>
		</div>
	
		<div class="item">
			<label for="edit-data-tags" class="bg input"><?php echo ucwords(AM_KEY_TAGS); ?> (comma separated)</label>
			<input id="edit-data-tags" class="bg input" type="text" name="edit[data][<?php echo AM_KEY_TAGS; ?>]" value="<?php echo $data[AM_KEY_TAGS]; ?>" onkeypress="return event.keyCode != 13;" />
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
				<?php echo $G->templateSelectBox('edit-theme_template', 'edit[theme_template]', $data[AM_KEY_THEME], $page->template); ?>
			</div><div id="hidden">
				<label for="edit-hidden" class="bg input">Visibility</label>
				<div class="checkbox bg input"><input id="edit-hidden" type="checkbox" name="edit[<?php echo AM_KEY_HIDDEN; ?>]"<?php 
					
						// Check checkbox
						if (isset($data[AM_KEY_HIDDEN]) && $data[AM_KEY_HIDDEN] && $data[AM_KEY_HIDDEN] != 'false') {
							echo ' checked';
						}; 
						
						// Disable for home page
						if ($page->path == '/') { 
							echo ' disabled'; 
						}
					
				?> />Hide page from navigation</div>
			</div>
		</div>
	
		<div class="item">
			<label for="edit-redirect" class="bg input">To redirect this page to another location instead of showing its content, just fill in any URL</label>
			<input id="edit-redirect" class="bg input" type="text" name="edit[data][<?php echo AM_KEY_URL; ?>]" value="<?php echo $data[AM_KEY_URL]; ?>" placeholder="optional" onkeypress="return event.keyCode != 13;" />
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

<script>
guiPages({
	title: '<?php echo $data[AM_KEY_TITLE]; ?>',
	path: '<?php echo AM_BASE_DIR . AM_DIR_PAGES . $page->path; ?>'
});
</script>

<?php 


$G->element('footer');


?>