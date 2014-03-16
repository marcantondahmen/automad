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


defined('AUTOMAD') or die('Direct access not permitted!');


?>
<!DOCTYPE html>
<html lang="en">
<head>
	  
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<title><?php echo $this->guiTitle; ?></title>

	<link href="<?php echo AM_BASE_URL; ?>/automad/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo AM_BASE_URL; ?>/automad/gui/css/bootstrap.automad.css" rel="stylesheet">
	
	<script type="text/javascript" src="<?php echo AM_BASE_URL; ?>/automad/lib/jquery/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="<?php echo AM_BASE_URL; ?>/automad/lib/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo AM_BASE_URL; ?>/automad/gui/js/automad.min.js"></script>
	
</head>


<body>
	
	<div id="noscript" class="container">
		<div class="row">
			<div class="col-md-4 col-md-offset-4">
				<div class="alert alert-info"><h3>JavaScript must be enabled!</h3></div>
			</div>
		</div>
	</div>
	
	<div id="script" class="container" style="display: none;">
		