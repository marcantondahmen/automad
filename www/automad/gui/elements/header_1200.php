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
<?php $this->element('header_items'); ?> 
</head>


<body>	

<div id="wrapper-1200">

<div class="container">
	
	<div class="box full">
		<h1 class="text"><a href="<?php echo AM_BASE_URL; ?>/automad"><b>Automad</b></a> <a href="<?php echo AM_BASE_URL; ?>/" target="_blank"><?php echo $this->siteName(); ?></a></h1>
	</div>

</div>

<div id="noscript" class="main"><div class="box"><h3 class="text bg">JavaScript must be enabled!</h3></div></div>

<div id="script" class="container" style="display: none;">
		
<script>
	$('#noscript').remove();
	$('#script').css('display', 'block');
</script>

<div class="box full">
	<?php $this->element('menu'); ?>
</div>

	