/*!
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


// File manager dialog
function ajaxFileManager(options) {
	
	
	var	defaults = 	{title: '', path: ''},
		settings = 	$.extend({}, defaults, options),
		fileManager = $('<div id="filemanager"></div>');


	// Function to position the dialog, after updating its html.
	function positionManager() {
		
		fileManager.dialog({
			position: { 
				my: 'center', 
				at: 'center top+35%', 
				of: window 
			} 
		});
		
	};

	
	// Replace the Manager's html.
	function populateManager(status) {
		
		// Temporary HTML while waiting for ajax response.
		fileManager.html('<div class="item bg text">Updating ...</div>' + fileManager.html());
		
		// Send request.
		$.post('ajax/list_files.php', {path: settings.path}, function(listFilesData) {

			// Build html out of the passed status message and the ajax response.
			if (status) {
				html = status + listFilesData.html;
			} else {
				html = listFilesData.html;
			}
			
			// Replace the Manager's html.
			fileManager.html(html);
			
			// Set focus to close button
			$('button#filemanager-close').focus();
	
			// Remove delete button when no files are found (no checkbox = no file)!
			if (fileManager.find('input[type="checkbox"]').length == 0) {
				$('button#filemanager-delete-selected').hide();
			} else {
				$('button#filemanager-delete-selected').show();
			}
			
			positionManager();
			
		}, 'json');
	
	};	


	// File upload.
	function ajaxFileUpload() {
	
		// Prevent the default action when a file is dropped on the window
		$(document).on('drop dragover', function (e) {
			e.preventDefault();
		});
		
		
		var	fileUploader = 	$('<form id="upload"><div id="upload-dropzone-text" class="bg"><h2>Click or drop files here!</h2></div></form>'),
			input = 	$('<input id="upload-input" type="file" name="files[]" multiple />').appendTo(fileUploader).hide(),
			dropzone = 	$('<div id="upload-dropzone" class="item bg"></div>').appendTo(fileUploader);
		
		
		function positionUpload() {
	
			fileUploader.dialog({ 
				position: { 
					my: 'center', 
					at: 'center top+35%', 
					of: window 
				} 
			});
	
		};
	
	
		function fileSize(bytes) {
		
			if (typeof bytes !== 'number') {
				return '';
			}

			if (bytes >= 1000000000) {
				return (bytes / 1000000000).toFixed(2) + ' GB';
			}

			if (bytes >= 1000000) {
				return (bytes / 1000000).toFixed(2) + ' MB';
			}

			return (bytes / 1000).toFixed(2) + ' KB';
	
		};
	
	
		// Create upload dialog.
		fileUploader.dialog({
			title: 'Upload Files', 
			width: 400,  
			resizable: false, 
			modal: true, 
			buttons: [{
				id: 'upload-close',	
				text: 'Close',
				click: function() {
					$(this).dialog('close');
					$(this).remove();
					populateManager();
				}
			}],
			closeOnEscape: false
		});
	
		positionUpload();
	
	
		// Use dropzone to open file browser.
		dropzone.click(function() {	
			fileUploader.find('input').click();
			return false;
		});

	
		// Init fileupload plugin.
		input.fileupload({
		
			url: 'ajax/upload_files.php',
			dataType: 'json',
			dropZone: dropzone,
			sequentialUploads: true,
			singleFileUploads: true,
			
			// Send also the current page path.
			formData: [{name: 'path', value: settings.path}],
		
			add: function(e, data) {
		
				var	text = '';
			
				// As fallback when using IframeTransport and the files are uploaded in one go, the text will include all filenames and sizes.
				// In the normal case that always will be only one elements, since the all files from a selection are sent in a single request each.
				$.each(data.files, function(i) {
					text = text + '<b>' + data.files[i].name + '</b><br />' + fileSize(data.files[i].size) + '<br />';
				});
		
				data.context = $('<div class="item"></div>').appendTo(fileUploader);
		
				$('<div class="item text bg filename">' + text + '<span class="status"></span></div>').appendTo(data.context);	
				$('<div class="item bg progress"><div class="bg bar"></div></div>').appendTo(data.context);
			
				data.context.find('.status').text('Waiting ...');
				data.context.find('.bar').height('5px').width('0px');
			
				data.submit();
				
				positionUpload();
				
			    	// Disable the close button for every added file.
				// When all uploads are done, the button gets enabled again (always callback).
			    	$('#upload-close').text('Uploading ...').prop('disabled', true);
			
			},
		
			progress: function(e, data){

				var 	progress = parseInt(data.loaded / data.total * 100, 10);

				data.context.find('.status').text(progress + ' %');
				data.context.find('.bar').animate({width: progress + '%'}, 300, function() {
				
					if (progress == 100){
						data.context.find('.progress').remove();		
						positionUpload();
					}
				
				});
			 
			},
			
			always: function (e, data) {
				
				// If all uploads are done, or iframe transport is used, enable the close button again.
				// Check first, if iframe-transport is used for upload, otherwise "input.fileupload('active')" is not available.
				if (data.dataType.indexOf('iframe') < 0) {
				
					// Check for running uploads.
					if (input.fileupload('active') <= 1) {
						$('#upload-close').text('Close').prop('disabled', false);
					}
				
				} else {
					
					$('#upload-close').text('Close').prop('disabled', false);
				
				}
				
		    	},
		
			done: function (e, data) {
				
				data.context.find('.status').html(data.result.status);	
		    	
			},
			
			fail: function (e, data) {
			
				data.context.find('.status').text('Upload failed!');
			
			}
		
		});
	
	}


	// Create dialog.		
	fileManager.html('<div class="item bg text">Getting files ...</div>').dialog({
		
		title: settings.title, 
		width: 600,  
		resizable: false, 
		modal: true, 
		buttons: [
			{
				id: 'filemanager-delete-selected',
				text: 'Delete Selected',
				click: function() {
					$formData = $(this).find('form').serialize();
					$.post('ajax/delete_files.php', $formData, function(deleteFilesData) {
						populateManager(deleteFilesData.html);
					}, 'json');
				}
			},
			{
				text: 'Upload Files',
				click: function() {
					ajaxFileUpload(settings.path);
				}
			},
			{
				id: 'filemanager-close',
				text: 'Close',
				click: function() {
					fileManager.dialog('close');
					fileManager.remove();
				}
			}
		] 
		
	});


	// Populate the manager the first time.
	populateManager();
	
		
}





// Run all needed JS for the accounts page
function guiAccounts() {
	
	
	$('#delete').submit(function (e) {
		
		e.preventDefault(); 
		
		$('<div><span class="text">Do you really want to delete the selected users?</span></div>').dialog({
			
			title: 'Delete Users', 
			width: 300, 
			position: { 
				my: 'center', 
				at: 'center top+35%', 
				of: window 
			}, 
			resizable: false, 
			modal: true, 
			buttons: {
				Yes: function () {
					e.target.submit();
				}, 
				No: function () {
					$(this).dialog('close');
					$(this).remove();
				}
			}
			
		});
		
	});
	
	
}


// Run all needed JS for the pages page
function guiPages(page) {
	
	
	var	editFormHasChanged = false;
	
	
	(function setupAddForm() {
		
		
		var	addPage =	$('#add');
		
		
		addPage.submit(function(e) {
		
			e.preventDefault();
		
			// Check if page has changed already
			if(!editFormHasChanged) {
		
				var	addPageDialog =	$('#add-dialog').dialog({
				
						title: 'Add Subpage', 
						width: 398, 
						position: { 
							my: 'center', 
							at: 'center top+35%', 
							of: window 
						}, 
						resizable: false, 
						modal: true, 
						buttons: {
							Add: function () {
							
								var	title = 		$(this).find('input[name="title"]').val(),
									theme_template =	$(this).find('select[name="theme_template"]').val();
							
								if (title != '') {
							
									addPage.find('input[name="add[title]"]').attr('value', title);
									addPage.find('input[name="add[theme_template]"]').attr('value', theme_template);
									e.target.submit();
								
								}
							
							}, 
							Cancel: function () {
								$(this).dialog('close');
							}
						}
				
				
					});
					
			} else {
				
				$('<div><span class="text">To be able to add a subpage to the current page, you must save or discard your latest changes first!</span></div>').dialog({
				
					title: 'Unsaved Changes', 
					width: 300, 
					position: { 
						my: 'center', 
						at: 'center top+35%', 
						of: window 
					}, 
					resizable: false, 
					modal: true, 
					buttons: {
						Close: function () {
							$(this).dialog('close');
							$(this).remove();
						}
					}
				
				});
				
			}
			
		});
		
		
	})();
	
			
	(function setupEditForm() {
		
		
		var	editPage = 	$('#edit'),
			addCustomVar = 	$('#edit-addCustom'),
			
			// Add delete-buttons for each custom variable 
			addRemoveCustomVariableButtons = function() {
				
				var	buttonClass = 'removeCustom';	
				
				// Remove existing Buttons first, in case function got called already (for example when adding variables)
				editPage.find('a.' + buttonClass).remove();	
			
				// Add buttons again
				$('.custom').each(function() {
				
					var removeButton = $('<a href="#" class="' + buttonClass + '">Remove</a>').prependTo($(this));
				
					removeButton.click(function() {
						$(this).parent().remove();
					})
				
				});
			
			};
		
		
		// Add the remove-button to pre-existing custom vars
		addRemoveCustomVariableButtons();
		
		
		// Register changes or user input.
		editPage.find('input, textarea, select').change(function() {
			
			editFormHasChanged = true;
			
		});
	
		editPage.find('a, input[type="button"]').click(function() {
			
			editFormHasChanged = true;	
		
		});
		
		
		// Check if page has a title, before saving
		editPage.submit(function (e) {
		
			e.preventDefault(); 
		
			if ($(this).find('#edit-data-title').val()) {
			
				e.target.submit();
			
			} else {
			
				$('<div><span class="text">The page has not title!</span></div>').dialog({
			
					title: 'Title Missing', 
					width: 300, 
					position: { 
						my: 'center', 
						at: 'center top+35%', 
						of: window 
					}, 
					resizable: false, 
					modal: true, 
					buttons: {
						Close: function () {
							$(this).dialog('close');
							$(this).remove();
						}
					}
			
				});
			
			}
		
		});
		
		
		// Setup button to add custom variables to the edit form.
		addCustomVar.click(function() {
		
		
			var  	editPrefix = 		'edit-data-';
			
			
			$('<form onkeypress="return event.keyCode != 13;"><input class="item bg input" type="text" name="newkey" value="" placeholder="Variable Name" /></form>').dialog({

				title: 'Add Custom Variable', 
				width: 398, 
				position: { 
					my: 'center', 
					at: 'center top+35%', 
					of: window 
				}, 
				resizable: true, 
				modal: true, 
				buttons: {
					
					Add: function() {
						
						var 	newkey = $(this).find('input[name="newkey"]').val().replace(/[^\w]/g, '_').toLowerCase();
						
						// Check if variable exists already.
						if ($('#' + editPrefix + newkey).length == 0){
							
							// Check if variable name is not empty.
							if (newkey != '') {
								
								$('<div class="custom item"><label for="' + editPrefix + newkey + '" class="bg input">' 
									+ newkey.charAt(0).toUpperCase() + newkey.slice(1) 
									+ '</label><textarea id="' + editPrefix + newkey + '" class="bg input" name="edit[data][' + newkey + ']" rows="8"></textarea></div>')
									.insertBefore(addCustomVar);
								
								addRemoveCustomVariableButtons();
								$(this).dialog("close");
								$(this).remove();
								
							}
							
						} else {
							
							$(this).dialog({
								title: 'Variable exists already!'
							});
							
						}
						
					},
					
					Cancel: function() {
						
						$(this).dialog('close');
						$(this).remove();
						
					}
					
				}
			
			});
				
						
		});
	
		
	})();
	

	(function setupDeleteForm() {
		
	
		// Confirmation dialog when deleting a page
		$('#delete').submit(function (e) {
		
			e.preventDefault(); 
		
			$('<div><span class="text">Do you really want to delete the page <b>"' + page.title + '"</b> and all of its subpages?</span></div>').dialog({
			
				title: 'Deleting "' + page.title + '"', 
				width: 300, 
				position: { 
					my: 'center', 
					at: 'center top+35%', 
					of: window 
				}, 
				resizable: false, 
				modal: true, 
				buttons: {
					Yes: function () {
						e.target.submit();
					}, 
					No: function () {
						$(this).dialog('close');
						$(this).remove();
					}
				}
			
			});
		
		});
		
		
	})();
	
	
	(function setupMoveForm() {
		
		
		var	movePage = 	$('#move'),
			moveTree = 	$('#move-tree');
		
		
		movePage.submit(function(e) {
		
			e.preventDefault();
		
			// Check if page has changed already
			if(!editFormHasChanged) {
		
				var 	movePageParentUrlInput = $(this).find('input[name="move[parentUrl]"]'),
					moveTreeDialog = moveTree.dialog({
					
						title: 'Move "' + page.title + '" and its subpages to:', 
						width: 398, 
						position: { 
							my: 'center', 
							at: 'center top+35%', 
							of: window 
						}, 
						resizable: true, 
						modal: true, 
						buttons: {
							Move: function() {
								if (movePageParentUrlInput.attr('value') != '') {
									e.target.submit();
								}
							},
							Cancel: function() {
								$(this).dialog('close');
							}
						}
					
					});	
			
				moveTree.find('form').submit(function(e) {
		
					e.preventDefault();
					
					// Set value for parent url in move-form
					movePageParentUrlInput.attr('value', $(this).find('input[name="url"]').val());
			
					// Remove .selected from possible previous click
					moveTree.find('.selected').removeClass('selected');
			
					// Add .selected to clicked element in tree
					$(this).find('input[type="submit"]').addClass('selected');
			
				});
		
			} else {
			
				$('<div><span class="text">To be able to move <b>"' + page.title + '"</b>, you must save or discard your latest changes first!</span></div>').dialog({
				
					title: 'Unsaved Changes', 
					width: 300, 
					position: { 
						my: 'center', 
						at: 'center top+35%', 
						of: window 
					}, 
					resizable: false, 
					modal: true, 
					buttons: {
						Close: function () {
							$(this).dialog('close');
							$(this).remove();
						}
					}
				
				});
			
			}
		
		});
		
		
	})();
	
	
	(function setupSiteTree() {
		
		
		var 	tree = 	$('#tree');
		
		
		// Prevent leaving without saving
		tree.find('form').submit(function(e) {
		
			if (editFormHasChanged) {
			
				e.preventDefault();
			
				$('<div><span class="text">Do you really want to leave without saving your changes for <b>"' + page.title + '"</b>?</span></div>').dialog({
				
					title: 'Unsaved Changes', 
					width: 300, 
					position: { 
						my: 'center', 
						at: 'center top+35%', 
						of: window 
					}, 
					resizable: false, 
					modal: true, 
					buttons: {
						Yes: function () {
							e.target.submit();
						}, 
						No: function () {
							$(this).dialog('close');
							$(this).remove();
						}
					}
				
				});
			
			}
		
		});
		
		
	})();
	
	
	(function setupFileManager() {
	
			
		$('#files').find('input[type="button"]').click(function() {
			
			ajaxFileManager({
				title: 'Manage Files for "' + page.title + '"',
				path: page.path
			});
		
		});
		
	
	})();
	

}