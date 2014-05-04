#Gallery

The Gallery extensions creates a list of preview images which can be viewed in full size by clicking on the thumbnail. The images to be included can be specified by a glob pattern.    
If an image contains an Exif description tag, that tag will be used as the overlay's caption.

---

###Markup

To use the Gallery extension, simply put 

	x(Gallery)
	
somewhere in your markup.

---

###Options

There are several parameters to modify the gallery:

- glob:		The file pattern
- width:	The thumbnails' width
- height:	The thumbnails' height

---

###Example

	x(Gallery {
		glob: "/pages/*/*/*.jpg", 
		width: 250, 
		height: 250
	}) 
	
It is also possible to use a page variable for any of the options:

	x(Gallery {
		glob: p(glob), 
		width: p(width), 
		height: p(height)
	}) 