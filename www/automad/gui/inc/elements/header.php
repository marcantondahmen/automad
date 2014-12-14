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
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


defined('AUTOMAD') or die('Direct access not permitted!');


?>
<!DOCTYPE html>
<html lang="en">
<head>
	  
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="robots" content="noindex">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	
	<title><?php echo $this->guiTitle; ?></title>

	<link href="<?php echo AM_BASE_URL; ?>/automad/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo AM_BASE_URL; ?>/automad/lib/malihu-custom-scrollbar/jquery.mCustomScrollbar.min.css" rel="stylesheet">
	<link href="<?php echo AM_BASE_URL; ?>/automad/gui/css/automad_gui.min.css" rel="stylesheet">
	
	<script type="text/javascript" src="<?php echo AM_BASE_URL; ?>/automad/lib/jquery/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="<?php echo AM_BASE_URL; ?>/automad/lib/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo AM_BASE_URL; ?>/automad/lib/malihu-custom-scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
	<script type="text/javascript" src="<?php echo AM_BASE_URL; ?>/automad/gui/js/automad_gui.min.js"></script>
	
</head>


<body>
	
	<div id="noscript" class="container">
		<div class="column content">
			<div class="inner">
				<div class="alert alert-warning"><h3>JavaScript must be enabled!</h3></div>
			</div>
		</div>
	</div>
	
	<div class="container hidden-md hidden-lg">
		<div class="column content">
			<div class="inner">
				<div class="alert alert-warning"><h3>Your browser window is too small!</h3></div>
			</div>
		</div>
	</div>

	<?php $this->element('navbar'); ?> 
	
	<div id="script" class="container hidden hidden-sm hidden-xs">
		
		<?php $this->element('sidebar'); ?> 
