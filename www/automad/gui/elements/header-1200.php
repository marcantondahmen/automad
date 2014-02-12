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
 *	The Automad GUI header for 1200px wide pages. 
 */


namespace Core;


defined('AUTOMAD') or die('Direct access not permitted!');


// Setup basic debugging
Debug::reportAllErrors();
Debug::timerStart();


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<title><?php echo $this->siteName(); ?> / Automad / <?php echo $this->guiTitle; ?></title>
<?php $this->element('header-items'); ?> 
</head>


<body>	

<div id="wrapper-1200">

<div class="logo"><?php 
	 
	$logo = new Image(AM_BASE_DIR . '/automad/gui/images/am-logo.png', 70, 70, false);
	echo '<img src="' . AM_BASE_URL . $logo->file . '" width="' . $logo->width . '" height="' . $logo->height . '">';

?></div>	

<div class="title"><h1><a href="<?php echo AM_BASE_URL; ?>/automad"><b>Automad</b> | <?php echo $this->siteName(); ?></a></h1></div>

<?php $this->element('menu'); ?>

<div id="noscript" class="main"><div class="box"><h3 class="text bg">JavaScript must be enabled!</h3></div></div>

<div class="main" style="display: none;">
		
<script>
	$('#noscript').remove();
	$('.main').css('display', 'block');
</script>
	