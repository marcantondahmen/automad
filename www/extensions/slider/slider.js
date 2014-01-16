/*!
 *	SLIDER
 *	Extension for the Automad CMS
 *
 *	Copyright (c) 2013 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


$(document).ready(function() {
	
	
	// In case there are multiple silders
	$('.slider').each(function() {
		
		var	images = 	$(this).find('img'),
			duration =	$(this).data('duration'),
			interval =	images.length * duration,
			fade =		800;
		
		// Only start slider, if there are more than one image.
		if (images.length > 1) {
		
			// Hide all but the first
			images.not(':first').hide();
		
			// The full cycle of all images.
			var	cycle = 	function() {
					
							// Hide all but the first and the last, since the first is always visible and just gets overlayed by the following images
							// and the last one gets faded out slowly to simulate a nice cycle.
							images.not(':first').not(':last').hide();
							// Fade out last element from previous cycle to simulate fade in of first item.
							images.last().fadeOut(fade);
				
							images.each(function(i) {
						
								var	$this = $(this),
									d =	i * duration;
						
								setTimeout(function() {
									$this.fadeIn(fade);
								}, d);
						
							});
				
						};
			
			// Initial call.
			cycle();
		
			// Set interval.
			setInterval(function() {
				cycle();	
			}, interval);
	
		}
	
	});
	
	
});