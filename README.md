# Automad

A flat-file content management system and template engine

![Tag](https://img.shields.io/github/v/tag/marcantondahmen/automad?include_prereleases&sort=semver&color=222222)
![PHP](https://img.shields.io/packagist/dependency-v/automad/dist/php?color=222222)
![Language](https://img.shields.io/github/languages/top/marcantondahmen/automad?color=222222)
![Size](https://img.shields.io/github/languages/code-size/marcantondahmen/automad?color=222222)
![License](https://img.shields.io/github/license/marcantondahmen/automad?color=222222)
[![Twitter](https://img.shields.io/twitter/follow/automadcms?label=Follow)](https://twitter.com/automadcms)

![](https://raw.githubusercontent.com/marcantondahmen/media-files/master/automad-v2/readme-light.png)

## Installation

Automad can be installed using [Docker](https://docker.com), [Composer](https://getcomposer.org) or manually.

### Composer

The fastest way to get Automad up and running is to use Composer.

```bash
composer create-project automad/automad . v2.x-dev
```

Follow this [guide](https://automad.org/version-2#getting-started) to finish the installation and get started quickly.

### Docker

It is also possible to run Automad in a [Docker](https://hub.docker.com/r/automad/automad) container including **Nginx** and **PHP 8.3**.

```bash
docker run -dp 80:80 -v ./app:/app --name mysite automad/automad:v2
```

The first time you run the image, a new user account for the Automad dashboard will be created automatically. The account details will be logged by the running container. You can show these logs using the following command:

```bash
docker logs mysite
```

Your can now navigate to [localhost](http://localhost) to view your new site.

### Manual Download

Alternatively Automad can also be [downloaded](https://github.com/automadcms/automad-dist/archive/refs/heads/v2.zip) as a `.zip` file and installed manually. [Read more here.](https://automad.org/version-2#getting-started)

## Documentation

Take a look at the [documentation](https://automad.org) to get started with Automad.

## Packages

Visit the [Automad package browser](https://packages.automad.org) to get free themes and extensions for your Automad site.

## Community

Join the community, ask questions or start a discussion on the Automad [discussion platform](https://automad.org/discuss).

## Contributing

In case you are interested in contributing, the following types of contribution are welcome:

-   Improving [language packs](https://github.com/automadcms/automad-language-packs) by fixing translation errors or adding new languages
-   [Publishing packages](https://automad.org/developer-guide/publishing-packages) like themes or extensions to the Automad package [browser](https://packages.automad.org)
-   Giving feedback and helping to grow a [community](https://discuss.automad.org)
-   Reporting bugs or requesting features at [GitHub](https://github.com/marcantondahmen/automad/issues)

However, I do not exclude at this point using parts of Automad's source in future projects under different licenses. In order to avoid having to ask anybody for permission when doing so, I will not accept any contributions to **this** repository. Please understand that pull requests will therefore be ignored.

## Text Editors Plugins

To make the development of themes more efficient, plugins providing syntax highlighting and snippets for Automad's template language are available for the following editors:

-   [Neovim (Tree-Sitter)](https://github.com/automadcms/tree-sitter-automad)
-   [Visual Studio Code](https://marketplace.visualstudio.com/items?itemName=MarcAntonDahmen.automad)
-   [Atom](https://atom.io/packages/language-automad)
-   [Textmate 2](https://github.com/marcantondahmen/automad.tmbundle)

---

© 2013-2024 [Marc Anton Dahmen](https://marcdahmen.de)  
Released under the [MIT license](https://automad.org/license)
