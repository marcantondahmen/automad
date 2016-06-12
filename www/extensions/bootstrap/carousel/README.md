#Bootstrap/Carousel

The Carousel extension creates a slideshow using Twitter's Bootstrap framework out of all images matching a specified glob pattern.    

---

###Markup

To use the Carousel extension, simply add

	<@ bootstrap/carousel @>

somewhere to your template's HTML body markup.

---

###Dependencies

The Carousel extension requires *Twitter's Bootstrap* CSS and Javascript.
You can include all needed files by adding

	<@ bootstrap/css @>
	<@ bootstrap/js @>

to your template's head section.

---

### Options

There are several options available to modify the carousel:

- files: `/path/to/images` - The file pattern or list of files, relative or absolute path (default: `"*.jpg"`)
- width: `integer` - Image width in pixels (default: `400`)
- height: `integer` - Image height in pixels (default: `300`)
- fullscreen: `boolean` - Full width and height of parent element (for example body, default: `false`)
- order: `"asc"`, `"desc"` or `false` - Set the order of images (default: `false`)
- duration: `integer` - Duration in milliseconds for each image (default: `3000`)
- controls: `boolean` - Enable/disable controls (default: `true`)

The options must be specified in **JSON** format.

---

###Example

Carousel 850x450px, 3 seconds per slide and a variable for the glob pattern:

	<@ bootstrap/carousel {
		files: @{carousel_file_pattern},
		width: 850,
		height: 450,
		duration: 3000
	} @>

The same carousel without controls and a list of files:

	<@ bootstrap/carousel {
		files: "image1.jpg, image2.jpg",
		width: 850,
		height: 450,
		duration: 3000,
		controls: false
	} @>
