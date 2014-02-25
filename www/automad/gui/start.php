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
 *	The GUI Start Page. As part of the GUI, this file is only to be included via GUI::context().
 */


$C = new Cache();
$lastEdit = $C->getSiteMTime();


$this->guiTitle = $this->guiTitle . ' / Welcome';
$this->element('header');


?>

		<div class="row">

			<div class="col-md-4 col-md-offset-4">

				<div class="list-group">
					
					<?php $this->element('title'); ?>
					
					<div class="list-group-item list-group-item-info">
						<h3>Welcome <strong><?php echo ucwords($this->user()); ?></strong></h3>
					</div>	
					
					<div class="list-group-item list-group-item-info">	
						<h5>Your site got modified the last time on<br /><strong><?php echo date('l, j. F Y, G:i', $lastEdit); ?>h</strong>.</h5>	
					</div>
					
					<a class="list-group-item" href="?context=system_settings"><h5><span class="glyphicon glyphicon-cog"></span> System Settings</h5></a>
					
					<a class="list-group-item" href="?context=edit_shared"><h5><span class="glyphicon glyphicon-globe"></span> Global Content &amp; Settings</h5></a>
					
					<a class="list-group-item" href="#" data-toggle="modal" data-target="#pagesModal"><h5><span class="label label-default"><?php echo count($this->collection); ?></span> Pages</h5></a>

					<!-- Modal -->
					<div class="modal fade" id="pagesModal" tabindex="-1" role="dialog" aria-labelledby="pagesModalLabel" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h4 class="modal-title" id="pagesModalLabel">Edit Page</h4>
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
					
					<a class="list-group-item" href="?context=logout"><h5><span class="glyphicon glyphicon-off"></span> Log Out "<?php echo ucwords($this->user()); ?>"</h5></a>
				
				</div>

			</div>
		
		</div>

<?php


$this->element('footer');


?>