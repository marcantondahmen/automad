To use the Carousel extension, simply put 

	x(Carousel)
	
somewhere in your HTML markup.

There are several options available to modify the carousel:

	- glob (default "*.jpg")
	- width (default 400)
	- height (default 300)
	- duration (default 3000)

The option can be specified in JSON format. 
It is also possible to pass any value as a normal page variable "p(variable)" to be defined differently for every page using the template.

Example:

	x(Carousel {
		glob: "p(carousel_file_pattern)", 
		width: 850, 
		height: 450, 
		duration: 3000
	}) 

For any matched file, a separate page variable with the name "carousel_caption_FILE" will be created within the markup. 
These variables can be used (also via the GUI) to add Markdown/HTML content as the file's caption.
