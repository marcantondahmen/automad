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
				}
			}
			
		});
		
	});
	
	
}


// Run all needed JS for the pages page
function guiPages() {
	
	
	var	editFormHasChanged = false;
	
	
	function setupAddForm() {
		
		
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
						}
					}
				
				});
				
			}
			
		});
		
		
	}
	
			
	function setupEditForm() {
		
		
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
						
						alert(newkey);
						
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
								
							}
							
						} else {
							
							$(this).dialog({
								title: 'Variable exists already!'
							});
							
						}
						
					},
					
					Cancel: function() {
						
						$(this).dialog('close');
						
					}
					
				}
			
			});
				
						
		});
	
		
	}
	

	function setupDeleteForm() {
		
		
		var	deletePage = $('#delete');
		
		
		// Confirmation dialog when deleting a page
		deletePage.submit(function (e) {
		
			var 	title = deletePage.attr('title');
		
			e.preventDefault(); 
		
			$('<div><span class="text">Do you really want to delete the page <b>' + title + '</b>?</span></div>').dialog({
			
				title: title, 
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
					}
				}
			
			});
		
		});
		
		
	}
	
	
	function setupMoveForm() {
		
		
		var	movePage = 	$('#move'),
			moveTree = 	$('#move-tree');
		
		
		movePage.submit(function(e) {
		
			e.preventDefault();
		
			// Check if page has changed already
			if(!editFormHasChanged) {
		
				var 	movePageParentUrlInput = $(this).find('input[name="move[parentUrl]"]'),
					moveTreeDialog = moveTree.dialog({
					
						title: 'Move this page and its subpages to:', 
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
			
				$('<div><span class="text">To be able to move this page, you must save or discard your latest changes first!</span></div>').dialog({
				
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
						}
					}
				
				});
			
			}
		
		});
		
		
	}
	
	
	function setupSiteTree() {
		
		
		var 	tree = 	$('#tree');
		
		
		// Prevent leaving without saving
		tree.find('form').submit(function(e) {
		
			if (editFormHasChanged) {
			
				e.preventDefault();
			
				$('<div><span class="text">Leave page without saving and discard changes?</span></div>').dialog({
				
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
						}
					}
				
				});
			
			}
		
		});
		
		
	}
	
	
	setupEditForm();
	setupAddForm();
	setupDeleteForm();
	setupMoveForm();
	setupSiteTree();
	
	
}