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


namespace Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The GUI Start Page. As part of the GUI, this file is only to be included via the GUI class.
 */


$C = new Cache();
$lastEdit = $C->getSiteMTime();


$this->guiTitle = $this->guiTitle . ' / Welcome';
$this->element('header');


?>

		<div class="row">

			<div class="col-md-6 col-md-offset-3">

				<?php $this->element('title'); ?>

				<div class="list-group">
					
					<div class="list-group-item">
						<h3>Welcome <strong><?php echo ucwords($this->user()); ?></strong></h3>
					</div>	
					
					<div class="list-group-item">	
						Last edit: <?php echo date('l, j. F Y, G:i', $lastEdit); ?>h.
					</div>
					
					<div class="list-group-item">
						Automad <?php echo AM_VERSION; ?>
					</div>
					
					<div class="list-group-item">	
						<span class="automad-status" data-automad-status="cache"></span><br />
						<span class="automad-status" data-automad-status="debug"></span>
					</div>
					
					<div class="list-group-item">
						<span class="automad-status" data-automad-status="users"></span>
					</div>
					
				</div>	
					
				<div class="list-group">	
				
					<a class="list-group-item" href="?context=system_settings">
						<h4><span class="glyphicon glyphicon-cog"></span> System Settings</h4>
					</a>
					
					<a class="list-group-item" href="?context=edit_shared">
						<h4><span class="glyphicon glyphicon-globe"></span> Global Content &amp; Settings</h4>
					</a>
					
					<a class="list-group-item" href="#" data-toggle="modal" data-target="#pagesModal">
						<span class="badge"><?php echo count($this->collection); ?></span>
						<h4><span class="glyphicon glyphicon-list-alt"></span> Pages</h4>
					</a>

					<!-- Modal -->
					<div class="modal fade" id="pagesModal" tabindex="-1" role="dialog" aria-labelledby="pagesModalLabel" aria-hidden="true">
						<div class="modal-dialog modal-sm">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h4 class="modal-title" id="pagesModalLabel">Select Page</h4>
								</div>
								<div class="modal-body">
									<?php echo $this->siteTree('', $this->collection, Parse::queryKey('url'), array('context' => 'edit_page'), false); ?>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
					</div>		
					
					<a class="list-group-item" href="?context=logout"><h5 class="text-muted"><span class="glyphicon glyphicon-off"></span> Log Out</h5></a>
				
				</div>

			</div>
		
		</div>

<?php


$this->element('footer');


?>