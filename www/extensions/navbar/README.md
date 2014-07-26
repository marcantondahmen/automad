#Navbar

The Navbar extension creates a responsive and collapsable Bootstrap Navbar for multiple levels, including an optional search box.

---

###Markup

To use the Navbar extension, simply add 

	@x(Navbar)
	
somewhere to your template's HTML body markup.

---

###Dependencies

This extension requires *Twitter's Bootstrap* CSS and Javascript.
Since Bootstrap gets shipped with Automad, you can include all needed files by adding

	@t(bootstrapCSS)
	@t(bootstrapJS)
	
to your template's head section.	

---

### Options

There are several options available to modify the Navbar:

- fluid: `true` or `false` - Make the navbar span the full browser window width (default: `true`)
- fixedToTop: `true` or `false` - Lock the navbar into the top of a page, even when scrolling (default: `false`)
- brand: `Optional Brand Name` (default: sitename)
- logo: `/path/to/file` - Optional logo. The filename must be specified as a path starting at Automad's base directory (default: `false`)
- logoWidth: `integer` - Width of the logo in pixels (default: `100`)
- logoHeight: `integer` - Height of the logo in pixels (default: `100`)
- search: `Placeholder text for search box` or `false` to hide the search box - (default: `"Search"`)
- searchPosition: `"left"` or `"right"` - Position of the search box - (default: `"left"`)
- levels: `integer` - Maximum number of levels to be displayed (default: `2`)

The options must be specified in **JSON** format. 
It is also possible to pass any value as a normal page variable "p(variable)" or shared (side-wide) variable "s(variable)".

---

###Example

Centered navbar with optional brand as shared variable:

	@x(Navbar {
		fluid: false,
		brand: @s(brand),
		search: "Search"
	})
	
Full-width, fixed to the top and with an optional logo as shared variable, but without a search box:

	@x(Navbar {
		fixedToTop: true,
		logo: @s(logo),
		logoWidth: 300,
		search: false
	})