#Gallery

The Gallery extensions creates a list of preview images which can be viewed in full size by clicking on the thumbnail. The images to be included can be specified by a glob pattern.    
If an image contains an Exif description tag, that tag will be used as the overlay's caption.

---

###Markup

To use the Gallery extension, simply put 

	@x(Gallery)
	
somewhere in your markup.

---

###Options

There are several parameters to modify the gallery:

- files: `/path/to/images` - The file pattern or list of files, relative or absolute path (default: `"*.jpg"`)
- width: `integer` - The thumbnails' width in pixels (default: `200`)
- height: `integer` - The thumbnails' height in pixels (default: `200`)
- crop: `boolean` - Crop image to defined width and height (default: `true`)
- order: `"asc"`, `"desc"` or `false` - Set the order of images (default: `false`)
- class: `classname` - Custom class to wrap each image (default: `false`)
- firstWidth: `integer` - The thumbnails' width in pixels of the first item (default: `false`)
- firstHeight: `integer` - The thumbnails' height in pixels of the first item (default: `false`)
- firstClass: `classname` - Custom class to wrap the first image (default: `false`)
- enlargedWidth: `integer` - Maximum width of enlarged image (default: `false`)
- enlargedHeight: `integer` - Maximum height of enlarged image (default: `false`)

---

###Example

	@x(Gallery {
		files: "/pages/*/*/*.jpg, /shared/*.jpg", 
		width: 250, 
		height: 250
	}) 
	
It is also possible to use a page variable for any of the options:

	@x(Gallery {
		files: @p(files), 
		width: @p(width), 
		height: @p(height)
	}) 