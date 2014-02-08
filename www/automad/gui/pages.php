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

// Extract the prefix from the directory name.
$prefix = substr(basename($page->path), 0, strpos(basename($page->path), '.'));

// Determine the path to the data file.	
$pageFile = AM_BASE_DIR . AM_DIR_PAGES . $page->path . $page->template . '.' . AM_FILE_EXT_DATA;




// If the user edited, moved or deleted the page...
if (isset($_POST['action'])) {
	
	
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
		
		// Get the path of the data file by appending the basename of 'theme_template' to $page->path.
		$newPageFile = AM_BASE_DIR . AM_DIR_PAGES . $page->path . str_replace('.php', '', basename($edit['theme_template'])) . '.' . AM_FILE_EXT_DATA;
		
					
		// Build file content to be written to the txt file.
		$pairs = array();
		
		foreach ($data as $key => $value) {
			$pairs[] = $key . ': ' . $value;
		}
		
		$content = implode("\r\n" . AM_PARSE_BLOCK_SEPARATOR . "\r\n", $pairs);
		
		
		// Delete old file, in case, the template has changed.
		unlink($pageFile);
		
		// Save new file within current directory, even when the prefix/title changed. 
		// Renaming/moving is done in a later step, to keep files and subpages bundled to the current text file.
		file_put_contents($newPageFile, $content);
		
		
		// If the title or the prefix has changed, 
		// rename the page's directory including all children and all files, after saving.
		
		// Normalize and overwrite previous determined $prefix (above) to match the passed modification.
		if (!isset($edit['prefix'])) {
			$prefix = '';
		} else {
			$prefix = $edit['prefix'];
		}
		
		// Determine the actual path name by the (possibly updated) prefix & title, 
		// if the page is not the homepage (homepage will always be '/').
		if ($url != '/') {
			$newPagePath = rtrim(dirname($page->path), '/') . '/' . ltrim($prefix . '.', '.') . preg_replace('/[^\w]+/', '-', strtolower($edit['data']['title'])) . '/';
		} else {
			$newPagePath = '/';
		}
		
		// Check if the determined path differs from the existing path and rename the directory accordingly.
		if ($newPagePath != $page->path) {
			
			// In case the new path is already occupied by another page, prepend an index or append an index to the prefix (if existing).
			// The index has to be part of the prefix, since it must be also reproducible and also modifyable by the user.
			// That wouldn't be the case, if the index would simply be appended to the full path. For every re-save, that index would change (actaully alternating).
			// $origPrefix stores here the passed value for the prefix to be re-used within the while-loop.
			$i = 1;
			$origPrefix = $prefix;
			
			while (file_exists(AM_BASE_DIR . AM_DIR_PAGES . $newPagePath)) {
				
				// Build/update prefix - The '-' gets trimmed, if there is no prefix set.
				$prefix = ltrim($origPrefix . '-' . $i, '-');
				
				// Build path again - with updated prefix
				$newPagePath = rtrim(dirname($page->path), '/') . '/' . $prefix . '.' . preg_replace('/[^\w]+/', '-', strtolower($edit['data']['title'])) . '/';
			
				$i++;
				
			}
			
			rename(AM_BASE_DIR . AM_DIR_PAGES . $page->path, AM_BASE_DIR . AM_DIR_PAGES . $newPagePath);
			
		}
		
		
		// Clear the cache to make sure, the changes get reflected on the website directly.
		$C = new Core\Cache();
		$C->clear();
		
			
		// Rebuild Site object, since the file structure might be different now.
		$S = new Core\Site(false);
		$collection = $S->getCollection();
		

		// Find the page again, to get the correctly determined URL (Site::collectPages()).
		// Therefore the page needs to be found by path ($newPagePath).
		foreach ($collection as $p) {
			
			if ($p->path == $newPagePath) {
				$page = $p;
				break;
			}
			
		}
		
		// Set $url to the new URL form the rebuild Page object.
		$url = $page->url;
		
	}
	

} else {

	// If there was no action, just load the data from the txt file.
	$data = Core\Parse::textFile($pageFile);
	
}




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




// Find all templates of currently used site theme (set in site.txt).
$siteThemeTemplates = array_filter(glob(AM_BASE_DIR . AM_DIR_THEMES . '/' . $G->siteData[AM_KEY_THEME] . '/*.php'), function($file) {
	return false === in_array(basename($file), array('error.php', 'results.php'));
});

// Find all templates of all installed themes.
$templates = array_filter(glob(AM_BASE_DIR . AM_DIR_THEMES . '/*/*.php'), function($file) {
	return false === in_array(basename($file), array('error.php', 'results.php'));
});




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
			echo $G->siteTree('', $collection, $url, true); 
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
				<input id="edit-data-title" class="bg input" type="text" name="edit[data][<?php echo AM_KEY_TITLE; ?>]" value="<?php echo $data[AM_KEY_TITLE]; ?>" onkeypress="return event.keyCode != 13;" />
			</div><div id="prefix">
				<label for="edit-prefix" class="bg input">Index</label>
				<input id="edit-prefix" class="bg input" type="text" name="edit[prefix]" value="<?php echo $prefix; ?>" <?php if ($page->path == '/') { echo 'disabled'; } else { echo 'placeholder="optional"'; } ?> onkeypress="return event.keyCode != 13;" />
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

<script>guiPages();</script>

<?php 


$G->element('footer');


?>