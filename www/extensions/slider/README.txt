To use the Slider extension, simply put the following line into you template file:

	x{ Slider } 
			
			
That will create a slideshow with the default settings.
There are several parameters to modify the Slider:

- glob: 	the pattern to match image files to be displayed (for example "*.png" or "image*.jpg")
- width:	the width of the slider
- height:	the height
- duration:	the duration in milliseconds	


For example the following lines will create a Slider showing all JPG files of the current page in 850x450 pixels for 3 seconds.

	x{ Slider(
		glob: *.jpg, 
		width: 850, 
		height: 450, 
		duration: 3000
	)}

	
All the parameters are optional. To just have a Slider to show all images starting with "img" within the current page's folder, you can simply use:

	x{ Slider (glob: img*.jpg) }

