#Slider

The Slider extensions creates a simple image slideshow.

---

###Markup

To use the Slider extension, simply put the following line into you template file:

	x(Slider) 
			
---

###Options			

There are several parameters to modify the Slider:

- glob: `/path/to/images/*` - File pattern (default: `"*.jpg"`)
- width: `integer` - Image width in pixels (default: `400`)
- height: `integer` - Image height in pixels (default: `300`)
- duration: `integer` - Duration in milliseconds for each image (default: `3000`)

---

###Examples

The following markup will create a Slider showing all JPG files of the current page in 850x450 pixels for 3 seconds.

	x(Slider {
		glob: "*.jpg", 
		width: 850, 
		height: 450, 
		duration: 3000
	})

	
All the parameters are optional. To just have a Slider to show all images starting with "img" within the current page's folder, you can simply use:

	x(Slider {glob: "img*.jpg"})

