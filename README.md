# Automad

A flat-file content management system and template engine.

![Tag](https://img.shields.io/github/v/tag/marcantondahmen/automad?include_prereleases&sort=semver&color=222222)
![PHP](https://img.shields.io/packagist/dependency-v/automad/automad/php?version=dev-master&color=222222)
![Language](https://img.shields.io/github/languages/top/marcantondahmen/automad?color=222222)
![Size](https://img.shields.io/github/languages/code-size/marcantondahmen/automad?color=222222)
![License](https://img.shields.io/github/license/marcantondahmen/automad?color=222222)
[![Twitter](https://img.shields.io/twitter/follow/automadcms?label=Follow)](https://twitter.com/automadcms)

![](https://raw.githubusercontent.com/marcantondahmen/media-files/master/automad-v2/readme-light.png)

## Links

-   [Documentation](https://automad.org)
-   [Changelog](https://github.com/marcantondahmen/automad/blob/-/CHANGELOG.md)
-   [Discussion](https://automad.org/discuss)

## Installation

Note that this repository only contains source code. Please follow the instructions below in order to install a fully bundled
version of Automad using [Docker](https://docker.com) or [Composer](https://getcomposer.org).
It is also possible to manually [download](https://github.com/automadcms/automad-dist/archive/refs/heads/master.zip)
and [install](#manual-installation) Automad.

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

This will essentially make your site available at port `80` and mount a directory called `app` in the current working directory for data persistence.
A new user account for the Automad dashboard will be created automatically. The account details will be logged by the running container.
You can show these logs using the following command:

```bash
docker logs mysite
```

Your can now navigate to [localhost](http://localhost) to view your new site.

### Manual Installation

In case you are not able to use Docker or Composer, you can also deploy Automad manually.

1. Download a [distribution bundle](https://github.com/automadcms/automad-dist/archive/refs/heads/master.zip) and move the
   unpacked content to the document root of your webserver.
2. Make sure the PHP process has the permissions to write to the document root and its subdirectories including all installed files.
3. Visit the `/dashboard` route of your site and create the first user.

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
-   Giving feedback and helping to grow a [community](https://automad.org/discuss)
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
