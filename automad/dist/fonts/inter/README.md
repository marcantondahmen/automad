>The Typefaces project is now deprecated.
>
>@DecliningLotus created
[FontSource](https://github.com/fontsource/fontsource) which provides the
same functionality as Typefaces but with automated releases & richer
support for importing specific weights, styles, or language subsets.
>
>To start using Fontsource, replace in your package.json any instances of
"typeface-inter" with "fontsource-inter".
>
> Then change imports from "import 'typeface-inter'" to "import 'fontsource-inter/latin.css'".
>
>Typeface packages will continue working indefinitely so no immediate changes are necessary.
>
>Specifically for `typeface-inter` package, we have an ongoing [discussion here](https://github.com/ajmalafif/typeface-inter/issues/5) as to why this package decided to keep a different version than the rest of Typefaces packages. Feel free to share your thoughts there too.
>

# typeface-inter

The intent is to easily use [Inter](https://github.com/rsms/inter/) typeface on any [webpack](https://github.com/webpack) setup, like [Gatsby](https://github.com/gatsbyjs/gatsby) and [Create React App](https://github.com/facebook/create-react-app).


### Installing
```bash
npm install --save typeface-inter
```

## How-to use

Simply require the package in your project’s entry file:
```javascript
// Load Inter typeface
require('typeface-inter')
```

## License

This project is licensed under the SIL Open Font License 1.1 - see the [LICENSE.txt](LICENSE.txt) file for details.

## Acknowledgments

All credits to [Rasmus](https://github.com/rsms) for his creation of [Inter typeface](https://github.com/rsms/inter).

[Philip Belesky](https://github.com/philipbelesky) for his repo at [inter-ui](https://github.com/philipbelesky/inter-ui).

## About the Typefaces project.

Our goal is to add all open source fonts to NPM to simplify using great fonts in
our web projects. We’re currently maintaining 1036 typeface packages
including all typefaces on Google Fonts.

If your favorite typeface isn’t published yet, [let us know](https://github.com/KyleAMathews/typefaces)
and we’ll add it!