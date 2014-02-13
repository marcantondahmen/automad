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
 *	The Automad GUI header for 400px pages. 
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
	
<div id="wrapper-400">

<div class="logo"><?php 
	 
	$logo = new Image(AM_BASE_DIR . '/automad/gui/images/am-logo.png', 200, 200, false);
	echo '<img src="' . AM_BASE_URL . $logo->file . '" width="' . $logo->width . '" height="' . $logo->height . '">';

?></div>	

<div class="title"><h1><b>Automad</b></h1><h3><?php echo $this->siteName(); ?></h3><br /><h2><?php echo $this->guiTitle; ?></h2></div>

<div class="main">

