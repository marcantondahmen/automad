To use the Gallery extension, simply put 

	x(Gallery)
	
somewhere in your markup.

There are several parameters to modify the the gallery:

- glob:		The file pattern
- width:	The thumbnails' width
- height:	The thumbnails' height


For example:

	x(Gallery {
		glob: "/pages/*/*/*.jpg", 
		width: 250, 
		height: 250
	}) 