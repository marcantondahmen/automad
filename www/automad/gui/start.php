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


$this->guiTitle = $this->guiTitle . ' / ' . $this->tb['start_title'] . ' ' . ucwords($this->user());
$this->element('header');


?>

		<div class="row">

			<div class="col-md-6 col-md-offset-3">

				<?php $this->element('title'); ?>

				<div class="alert alert-info"><h3><?php echo $this->tb['start_title'] . ' ' . ucwords($this->user()); ?></h3></div>

				<div class="list-group">
					
					<div class="list-group-item text-muted">	
						<?php echo $this->tb['start_last_edit']; ?> <strong><?php echo date('l, j. F Y, G:i', $lastEdit); ?>h</strong>
					</div>
					
					<div class="list-group-item">	
						<span class="automad-status" data-automad-status="cache"></span><br />
						<span class="automad-status" data-automad-status="debug"></span>
					</div>
					
					<div class="list-group-item text-muted">
						Automad Version: <strong><?php echo AM_VERSION; ?></strong>
					</div>
					
				</div>	
					
				<div class="list-group">	
				
					<a class="list-group-item" href="?context=system_settings">
						<h5><span class="glyphicon glyphicon-cog"></span> <?php echo $this->tb['sys_title']; ?></h5>
					</a>
					
					<a class="list-group-item" href="?context=edit_shared">
						<h5><span class="glyphicon glyphicon-globe"></span> <?php echo $this->tb['shared_title']; ?></h5>
					</a>
					
					<a class="list-group-item" href="#" data-toggle="modal" data-target="#pagesModal">
						<span class="badge"><?php echo count($this->collection); ?></span>
						<h5><span class="glyphicon glyphicon-list-alt"></span> <?php echo $this->tb['pages_title']; ?></h5>
					</a>

					<!-- Modal -->
					<div class="modal fade" id="pagesModal" tabindex="-1" role="dialog" aria-labelledby="pagesModalLabel" aria-hidden="true">
						<div class="modal-dialog modal-sm">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h4 class="modal-title" id="pagesModalLabel"><?php echo $this->tb['pages_title']; ?></h4>
								</div>
								<div class="modal-body">
									<?php echo $this->siteTree('', $this->collection, Parse::queryKey('url'), array('context' => 'edit_page'), false); ?>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> <?php echo $this->tb['btn_close']; ?></button>
								</div>
							</div>
						</div>
					</div>		
					
					<a class="list-group-item" href="?context=logout"><h5><span class="glyphicon glyphicon-off"></span> <?php echo $this->tb['log_out_title']; ?></h5></a>
				
				</div>

			</div>
		
		</div>

<?php


$this->element('footer');


?>