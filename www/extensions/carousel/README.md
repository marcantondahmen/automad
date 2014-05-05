#Carousel

The Carousel extension creates a slideshow using Twitter's Bootstrap framework out of all images matching a specified glob pattern.    
For any matched image, a page variable `carousel_caption_FILENAME` will be added automatically. That variable then can be used for the image's caption content.

---

###Markup

To use the Carousel extension, simply add 

	x(Carousel)
	
somewhere to your template's HTML body markup.

---

###Dependencies

The Carousel extension requires *Twitter's Bootstrap* CSS and Javascript.
Since Bootstrap gets shipped with Automad, you can include all needed files by adding

	t(bootstrapCSS)
	t(bootstrapJS)
	
to your template's head section.

---

### Options

There are several options available to modify the carousel:

- glob: `/path/to/images/*` - File pattern (default: `"*.jpg"`)
- width: `integer` - Image width in pixels (default: `400`)
- height: `integer` - Image height in pixels (default: `300`)
- duration: `integer` - Duration in milliseconds for each image (default: `3000`)

The options must be specified in **JSON** format. 
It is also possible to pass any value as a normal page variable "p(variable)" to be defined differently for every page using the template.

---

###Example

	x(Carousel {
		glob: p(carousel_file_pattern), 
		width: 850, 
		height: 450, 
		duration: 3000
	}) 


