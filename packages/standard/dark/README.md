# Dark

The Dark theme is a clean and elegant multi-purpose theme. It provides several options to display your content. Please read below more about the main concepts of this theme.

![](https://raw.githubusercontent.com/marcantondahmen/media-files/master/automad/themes/dark.png)

- [Templates](#templates)
- [Colors](#colors)
- [Writing Content](#writing-content)
- [Pagelist Configuration](#pagelist-configuration)
	- [Related Pages](#related-pages)
- [Title, Navigation, Filtering and Sorting](#title-navigation-filtering-and-sorting)
- [Logo and Brand](#logo-and-brand)
- [Labels](#labels)
- [Date Formats](#date-formats)
- [Social Media Cards](#social-media-cards)
- [Header and Footer Items](#header-and-footer-items)
- [Slideshows](#slideshows)

## Templates

The following templates are included in this theme.

| Name | Description |
| ---- | ----------- |
| Blog | A blog page grid template. Articles are displayed along with their teaser image, date, tags and the first paragraph. |
| Portfolio | A portfolio page grid template. Projects are displayed along with their teaser image, date and tags. |
| Post | A blog post template. Related pages are displayed below the main content. |
| Project | A project page template. Related pages are displayed below the main content. |
| Sidebar Left | A multi-purpose template with a navigation sidebar on the left. |
| Sidebar Right | A multi-purpose template with a navigation sidebar on the right. |

## Colors

While there is a light and a dark theme serving as presets, all the colors used in the templates are fully customizable.

| Name | Description |
| ---- | ----------- |
| Color Text | A custom override text color |
| Color Bg | A custom override background color |
| Color Border | A custom override border color |
| Color Muted | A custom override color for muted and hovered text |
| Color Panel Bg | A custom override color for panel backgrounds |
| Color Code | A custom override code color |

## Writing Content

There are two ways of writing content using this theme &mdash; **Blocks** and **Markdown** formatted text. 
Please note that in case the **+Main** blocks variable has any content, the Markdown variables **Text** and **Text Teaser** are ignored! They only serve as an alternative and fallback content. 

| Name | Description |
| ---- | ----------- |
| +Main | The main content block area. |
| Text | An alternative content variable. It is only displays as long as the +Main variable is empty. |
| Text Teaser | An alternative teaser variable. It is only displayed as long as the +Main variable is empty. |
		
## Pagelist Configuration

This theme offers multiple options and two templates &mdash; **Blog** and **Portfolio** &mdash; to create pagelists. The following options can be used to control the content of such a pagelist. Pagelist templates can also be used to create a search results page.

| Name | Description |
| ---- | ----------- |
| Show All Pages In Pagelist | If checked, the pagelist includes all pages instead of just the direct children. |
| Filter Pagelist By Url | Filters the pagelist by URLs matching a given regular expression like for example `(/portfolio|/blog)`. |
| Notification No Search Results | The notification text for empty search results. |
| Items Per Page | The maximum number of pages to be displayed in a pagelist at once. In case there are more pages to be shown, a pagination navigation will show up below automatically. |
| Show Pages Below | Show only direct children of a custom local URL like `/custom/page` |
| Url Search Results | The local URL of the pagelist page to be used as the search results page. Note that the search field in the menu is only enabled in case an URL is defined. |
| Url Tag Link Target | The target page to navigate to when clicking a tag. By default the parent page is used. |
| Image Teaser | The filename or glob pattern for the image to be used as the teaser image in a pagelist. |
| Hide Thumbnails | Hide teaser images in page grid. |
| Sort Pagelist | Sorting of the pages in a portfolio or blog pagelist. Note that the sorting and filtering buttons should be hidden in case this variable is defined! The default is 'date desc'. You can choose any other page variable in combination with an order (asc or desc) like ':path asc'. |
| Sort Related Pages | Sorting of the pagelist with related pages. The default is 'date desc'. You can choose any other page variable in combination with an order (asc or desc). |
| Use Alternative Pagelist Layout | Use an alternative layout for blog, portfolio or related pages pagelists. |

### Related Pages 

Related pages are pages having one or more tags in common with the current page. By default they show up at the bottom below the page content. They can be disabled by checking the `Hide Related Pages` checkbox.

## Title, Navigation, Filtering and Sorting

The following checkboxes can be used to control the visibility of pages and elements.

| Name | Description |
| ---- | ----------- |
| Hide Filters | Hide the filter and search reset buttons on a blog page. |
| Hide Filters And Sort | Hide filter, sort and search reset buttons on a portfolio page. |
| Hide In Menu | Hide the page from the main menu. |
| Hide Prev Next Nav | Hide the previous and next arrow navigation around the title. |
| Hide Title | Hide the page title. |
| Show In Footer | Add the page to the footer menu. |
| Show In Navbar | Add the page to the navbar menu. |

## Logo and Brand

To set the brand name, a navbar logo and favicons, use the following options.

| Name | Description |
| ---- | ----------- |
| Brand | The brand HTML, SVG or text to be used instead of a logo. |
| Image Logo | The path to your logo - this variable should be defined globally in the shared data section. |
| Logo Height | The height of your logo - this variable should be defined globally in the shared data section. |
| Favicon | The local path to the icon to be used as favicon. |
| Image Apple Touch Icon | The image to be used as the Apple touch icon. |
| Page Icon Svg | A little SVG icon representing a page in a pagelist card. |

## Labels

All labels, button text, placeholders and notification texts can be overriden or translated as needed. The following variables are therefore available.

| Name | Description |
| ---- | ----------- |
| Label Clear Search | Button text for clearing search results. |
| Label Realated | Label for related pages section. |
| Label Show All | Label text for filter button when no filter is selected. |
| Label Sort Date Asc | Label text in dropdown for sorting pages by date ascending. |
| Label Sort Date Desc | Label text in dropdown for sorting pages by date descending. |
| Label Sort Title Asc | Label text in dropdown for sorting pages by title ascending. |
| Label Sort Title Desc | Label text in dropdown for sorting pages by title descending. |
| Notification No Search Results | Notification text for an empty list of search results. |
| Placeholder Search | Placeholder text for the search field of the main menu. |  

## Date Formats

This theme uses two different date formats. One for blog posts and another one for project pages. It is possible to override those formats to change the way a date appears on a page. Both, PHP's [strftime()](https://www.php.net/manual/en/function.strftime.php#refsect1-function.strftime-parameters) and [date()](https://www.php.net/manual/en/function.date.php#refsect1-function.date-parameters) formats are supported. Note that the locale options can be only used together with the strftime() syntax.

| Name | Description |
| ---- | ----------- |
| Format Date Post | The format for displaying the date of a post. |
| Format Date Project | The format for displaying the date of a project page. |
| Locale | The locale information to format date and time according to like `en_EN` or `de_DE` |

## Social Media Cards

This theme automatically generates preview cards when a page is linked in Twitter, Facebook or other social networks. The following options can be used to control the content of those cards.

| Name | Description |
| ---- | ----------- |
| Meta Description | An optional meta description to be used for Twitter, Facebook or similar social network cards. |
| Meta Title | An optional meta title to be used for the browser title bar and links used on Twitter, Facebook or similar social networks. |
| Og Image | A glob pattern to select a preview image for Twitter, Facebook or similar social network cards. This could be for example `*.png, *.jpg`. |

## Header and Footer Items

Sometimes it is required to add custom Javascript or CSS to one or more pages. This could be for example the case if you would want to add a Google Analytics tracking snippet to your site. Therefore this theme provides two variables for that purpose. The itemsHeader variable lets you add all kind of header items right before the closing `</head>` tag. To add any HTML or JS right before the closing `</body>` tag you can use the itemsFooter variable.

| Name | Description |
| ---- | ----------- |
| Items Header | Items to be inserted before the closing `</head>` tag. |
| Items Footer | Items to be inserted before the closing `</body>` tag. |

## Slideshows

As described before, it is either possible to used the block editor or the traditional Markdown editor to create content. Since the markdown editor is not able to create content like a slideshow, there are two separate variables available for project pages and posts to insert a slideshow right after the teaser when using Markdown.

| Name | Description |
| ---- | ----------- |
| Images Slideshow | One or more glob patterns to define the images showing up in the slideshow. |
| Slideshow Height | The relative height of the slideshow depending on the width. |