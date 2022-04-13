# Baker

An elegant documentation and blogging theme for [Automad](https://automad.org) based on the official Automad documentation theme. Check out a live preview [here](https://baker.dev.automad.org).

![](https://raw.githubusercontent.com/marcantondahmen/media-files/master/themes/baker/post-sidebar-masonry.png)

- [Templates](#templates)
- [Blog Configuration](#blog-configuration)
- [Pagelist Blocks](#pagelist-blocks)
- [Search and Filter](#search-and-filter)
- [Social Icons](#social-icons)
- [Options](#options)

## Templates

Baker ships with the following templates to layout your content:

| Name | Description |
| --- | --- |
| Blog | A full-width landing page template, displaying a pagelist in a masonry grid |
| Blog Sidebar | A blog page with a sidebar on the left, displaying a paglist in a masonry grid |
| Pagelist Sidebar | A simple paglist page with a sidebar |
| Post | A full-width page |
| Post Sidebar | A page with a sidebar |

## Blog Configuration

There are several options to customize the layout of a blog's pagelist and the content of the displayed pages. 

| Name | Description |
| --- | --- |
| Hide Date | Hides all dates on the page including the dates in the pagelist |
| Hide Thumbnails | Hides the preview image of the displayed pages |
| Show All Pages In Pagelist | Includes all pages in the pagelist instead of only the childrens |
| Small Pagelist Grid | Use a three-column grid on full-width pages and larger devices |
| Stretch Thumbnails | Stretches the preview images to the borders of the grid |
| Filter Pagelist by Url | Filters the pagelist by a regex to match the URLs of the pages |
| Items Per Page | Limits the number of pages to a given value and shows a pagination navigation in case there are more pages to be displayed |
| Sort Pagelist | Defines the sorting of the pagelist &mdash; the sorting type can be defined by providing a pair of a variable and an order like `date desc` or `:path asc` |
| Url Context For Pagelist | In case `Show All Pages In Pagelist` is turned off, any page can be defined here to be the parent page for the children that are included in the pagelist | 

## Pagelist Blocks

Baker provides seven different templates for the pagelist block. Check out the following list below:

| Name | Description |
| --- | --- |
| Masonry | A masonry grid with two columns, displaying title, date, tags, a preview image and the first paragraph of a page |
| Masonry Stretched | Same as the **Masonry** type but with stretched images |
| Masonry Text | Same as the **Masonry** type but without any image |
| Masonry Small | Same as the **Masonry** type but with three columns |
| Masonry Small Stretched | Same as the **Masonry Stretched** type but with three columns |
| Masonry Small Text | Same as the **Masonry Text** type but with three columns |
| Simple | A simple stacked pagelist showing the title and the first paragraph of a page |

## Search and Filter

With Baker it is pretty easy to create a page to show search results or filtered pagelists. Baker uses the same logic for showing pages that match a filter when clicking a tag below a page title and search results. Please follow these two steps to set up such a page:

1. Apply the **Blog**, **Blog Sidebar** or the **Pagelist Sidebar** to a page. 
2. Use that page's URL as value for the `Url Search Results` field in the global settings.

## Social Icons

You can add social icons linking to your profiles in navbar by providing profile URLs for Twitter, GitHub, Instagram and Facebook for the following fields in the global settings:

* `Url Facebook`
* `Url GitHub`
* `Url Instagram`
* `Url Twitter`

## Options

| Name | Description | Scope |
| --- | --- | --- |
| `Brand` | In case there is no logo defined, this field can be used for any kind of markup like HTML or SVG to serve as the brand of the site | All | 
| `Hide Date` | Hide the date | All | 
| `Hide Related Pages` | Hide related pages at the bottom of the Shared | All | 
| `Hide Thumbnails` | Hide thumbnails within a masonry page grid | All | 
| `Hide Title` | Hide the page title and tags | Page |
| `Show All Pages In Pagelist` | Show not only children but all pages in the pagelist | All | 
| `Show In Footer` | Show a link to this page in the footer | Page | 
| `Show In Navbar` | Show a link to this page in the navbar | Page | 
| `Simple Related Pagelist` | Use a simplified layout for the related pages list | All |
| `Small Pagelist Grid` | Use a 3-column grid for pagelists in full width templates | All | 
| `Stretch Thumbnails` | Stretch images in pagelists to the full width | All | 
| `Filter Pagelist By Url` | Filters the pagelist by URLs matching a given regular expression like for example '(portfolio\|blog)' | All | 
| `Format Date` | The format for displaying the date of a page &mdash; you can find more information about date formats [here](https://www.php.net/manual/en/datetime.format.php) | Shared | 
| `Image Apple Touch Icon` | The file to be used as Apple touch icon | Shared | 
| `Image Favicon` | The file to be used as favicon | Shared | 
| `Image Logo` | The logo image | Shared | 
| `Image Teaser` | A glob pattern or a filename to define the teaser image for a Shared | All | 
| `Items Footer` | Additional markup to be included right before the closing body tag | All | 
| `Items Header` | Additional markup to be included right before the closing head tag | All | 
| `Items Per Page` | Limit the items of the pagelist per Shared | All | 
| `Label Show All` | The text for show all button | Shared | 
| `Locale` | The locale information to format date and time according to like 'en_EN' or 'de_DE' | Shared | 
| `Logo Height` | The height of your logo | Shared | 
| `Meta Description` | An optional meta description to be used for Twitter, Facebook or similar social network cards | Page | 
| `Meta Title` | An optional meta title to be used for the browser title bar and links used on Twitter, Facebook or similar social networks | Page | 
| `Notification No Search Results` | Notification text for an empty list of search results | Shared | 
| `Og Image` | A glob pattern to select a preview image for Twitter, Facebook or similar social network cards. This could be for example `*.png, *.jpg` | All | 
| `Placeholder Search` | Placeholder text for the search field of the main menu | Shared | 
| `Sort Pagelist` | Sorting of the pages in a blog pagelist. Note that the sorting and filtering buttons should be hidden in case this variable is defined! The default is 'date desc'. You can choose any other page variable in combination with an order (asc or desc) like ':path asc' | All | 
| `Url Context For Pagelist` | Define an alternative URL to be used as the pagelist's parent page, instead of the current one | Page | 
| `Url Facebook` | If an URL to a Facebook page is given, a Facebook icon is added to the navbar | Shared | 
| `Url Github` | If an URL to a GitHub page is given, a GitHub icon is added to the navbar | Shared | 
| `Url Instagram` | If an URL to a Instagram page is given, a Instagram icon is added to the navbar | Shared | 
| `Url Search Results` | The URL of the search results page &mdash; leave empty to disable searching and hide the search field | Shared | 
| `Url Twitter` | If an URL to a Twitter page is given, a Twitter icon is added to the navbar | Shared | 