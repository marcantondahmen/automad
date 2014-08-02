/*!
 *	GALLERY
 *	Extension for the Automad CMS
 *
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


(function($){
		
	$.fn.Gallery = function() {
		
		var 	images =	$(this);
	
		if (images.length > 0) {
			
			var	slideshow =	$('<div class="gallerySlideshow"></div>').appendTo('body').hide(),
				overlay =	$('<div class="overlay"></div>').appendTo(slideshow).fadeTo(0,0.9),
				caption =	$('<div class="caption"></div>').appendTo(slideshow),
				captionText =	$('<div class="captionText"></div>').appendTo(caption),
				close = 	$('<a class="closeSlideshow" href="#"></a>').appendTo(slideshow),
				prev =		$('<a class="prevImage" href="#"></a>').appendTo(slideshow),
				next =		$('<a class="nextImage" href="#"></a>').appendTo(slideshow),
				isVisible =	false,		// Slideshow is visible
				origWidth,			// Original width of image
				origHeight,			// Original height of image
				current;			// index of current image
				
			
			// Caption 
			displayCaption = function(str) {
				captionText.html(str);
				caption.fadeIn(300);
			}
		
			
			// Navigation
			
			// Close
			close.click(function() {
				
				slideshow.fadeOut(300, function() {
					bigImage.remove();
				});
				isVisible = false;
				return false;
		
			});
					
			// Buttons
			prev.click(function() {
				if (current > 0) {
					current -= 1;			
				} else {
					current = images.length - 1;
				}
				changeImage(current);
				return false;
			});
			
			next.click(function() {
				if (images.length > (current + 1)) {
					current += 1;		
				} else {
					current = 0;	
				}
				changeImage(current);
				return false;
			});
			
			
			// Set image size & fade in 
			var	setImageSizeAndFadeIn = function() {
			
				var	bigWidth = 	bigImage.width(),
					bigHeight =	bigImage.height(),
					screenRatio =	slideshow.height() / slideshow.width(),
					bigRatio =	bigHeight / bigWidth;
							
				// Calculate size and position				
				if ((origWidth > slideshow.width()) || (origHeight > slideshow.height())) {	
					if (screenRatio > bigRatio) {
						bigWidth = slideshow.width();
						bigHeight = bigWidth * bigRatio;
					} else {
						bigHeight = slideshow.height();
						bigWidth = bigHeight / bigRatio;
					}
				}
				
				if ((slideshow.height() - bigHeight) != 0) {
					bigTop = ((slideshow.height() - bigHeight) / 2) + 'px';
				} else {
					bigTop = 0;
				}
				
				if ((slideshow.width() - bigWidth) != 0) {
					bigLeft = ((slideshow.width() - bigWidth) / 2) + 'px';
				} else {
					bigLeft = 0;
				}
				
				bigImage.css({
					width:	bigWidth + 'px',
					height:	bigHeight + 'px',
					top:	bigTop,
					left:	bigLeft
				});

				bigImage.fadeIn(300);
						
			};
		

			// Thumbnails 
			// Slideshow fade in			
			images.each(function(i) {
				
				var	$this =		$(this),
					url =		$this.attr('href'),
					dataCaption =	$this.find('img').data('caption');
					
				// Use image title as fallback for caption	
				if (!dataCaption) {
					dataCaption = 	$this.find('img').attr('title');
				}
						
				$this.click(function() {
				
					// Set current index to clicked image
					current = i;
							
					bigImage =	$('<img alt="">')
							.appendTo(slideshow)
							.hide()
							.one('load', function() {
								
								// Save original size after loading to determine real dimenaions after resizing
								origWidth = bigImage.width();
								origHeight = bigImage.height();
								
								setImageSizeAndFadeIn();
								displayCaption(dataCaption);
								isVisible = true;
							
							})
							.attr('src', url);		

					slideshow.fadeIn(300);	
							
					return false;
			
				});	
				
			});
			
		
			// Change image
			// Slideshow is open already
			var	changeImage = 	function(i) {
	
				var	url = 		images.eq(i).attr('href'),
					dataCaption =	images.eq(i).find('img').data('caption');	
				
				// Use image title as fallback for caption
				if (!dataCaption) {
					dataCaption = 	images.eq(i).find('img').attr('title');
				}
				
				// fade out curren image and caption
				bigImage.fadeOut(200);
				caption.fadeOut(200);
				
				// Wait for fade out to be finished
				setTimeout(function() {
						
					// Reset size by removing image
					bigImage.remove();
					
					// Recreate image again to determine new original size
					bigImage =	$('<img alt="">')
							.appendTo(slideshow)
							.hide()
							.one('load', function() {
								
								// Save original size
								origWidth = bigImage.width();
								origHeight = bigImage.height();	
								
								setImageSizeAndFadeIn();
								displayCaption(dataCaption);
		
							})
							.attr('src', url);
					
				}, 200);
				
			};
			

			// Resize
			$(window).resize(function() {
				if (isVisible) {
					setImageSizeAndFadeIn();
				}
			});
			
		}
		
	}
	
})(jQuery);


$(document).ready(function() {
			
	$('.gallery a').Gallery();
	
});


