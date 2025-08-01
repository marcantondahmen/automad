# Changelog

## [v2.0.0-alpha.38](https://github.com/marcantondahmen/automad/commit/e8be555fdc45618a2e45017562205b54b613a5f2)

Sat, 28 Jun 2025 18:24:18 +0200

### Breaking Changes

- merge shared and page customizations ([ad4c9022e](https://github.com/marcantondahmen/automad/commit/ad4c9022e6fbe58a175c93f159f2d51bb26b191c))

### New Features

- merge shared and page customizations ([ad4c9022e](https://github.com/marcantondahmen/automad/commit/ad4c9022e6fbe58a175c93f159f2d51bb26b191c))
- add option to shorten a field value in the public pagelist api ([07448f26b](https://github.com/marcantondahmen/automad/commit/07448f26b1613d4681e31b061e708ccb0772d0c0))
- improve composer compatibility ([f47d923b9](https://github.com/marcantondahmen/automad/commit/f47d923b9907ea6469f3f118c27ef9091fef51bd))
- render content in public pagelist api route and optionally strip tags ([e7f63afae](https://github.com/marcantondahmen/automad/commit/e7f63afaed6f26e7235bf4e38b673ad042567cca))

### Bugfixes

- fix missing alert box for empty trash ([e8be555fd](https://github.com/marcantondahmen/automad/commit/e8be555fdc45618a2e45017562205b54b613a5f2))
- ignore html tags when searching ([6a973a040](https://github.com/marcantondahmen/automad/commit/6a973a0402965c68061ab12fb45568bf222f5745))
- update ignored fields in search model ([026c8257c](https://github.com/marcantondahmen/automad/commit/026c8257c72e233103dfeb51c8c2bbd001fc97c6))

## [v2.0.0-alpha.37](https://github.com/marcantondahmen/automad/commit/b7160ecffc21255e0b9b4e090c14b2bc8f855be0)

Fri, 30 May 2025 21:30:59 +0200

### Breaking Changes

- use new curated registry for package manager that only includes a selection of themes and no longer supports extensions ([a300042dd](https://github.com/marcantondahmen/automad/commit/a300042dd6e8a0a98092236b092d3557856ffa97))

### New Features

- use new curated registry for package manager that only includes a selection of themes and no longer supports extensions ([a300042dd](https://github.com/marcantondahmen/automad/commit/a300042dd6e8a0a98092236b092d3557856ffa97))

### Bugfixes

- throw error during repository installation when auth is missing ([d41fa1bed](https://github.com/marcantondahmen/automad/commit/d41fa1bed8bac656ec6ffffcb0b87656aa0e623a))

## [v2.0.0-alpha.36](https://github.com/marcantondahmen/automad/commit/73da6a57aec98c8b3bc5c11cd304cfbd9a551513)

Sat, 17 May 2025 15:12:01 +0200

### Breaking Changes

- the standard php date format syntax can now also be used for localized dates, the icu syntax is no longer supported ([f904be4b1](https://github.com/marcantondahmen/automad/commit/f904be4b10dc81626b0679c7bf5d4ba757a97990))

### New Features

- the standard php date format syntax can now also be used for localized dates, the icu syntax is no longer supported ([f904be4b1](https://github.com/marcantondahmen/automad/commit/f904be4b10dc81626b0679c7bf5d4ba757a97990))

### Bugfixes

- clear cookies, local storage and session storage after reloading page when consent is revoked ([89e3eb13e](https://github.com/marcantondahmen/automad/commit/89e3eb13e7b52f257afb6b12fdd3dbd5a58eb53c))

## [v2.0.0-alpha.35](https://github.com/marcantondahmen/automad/commit/8ad5c216bc5e8ecccfd1c5c821f91986a2fc4029)

Wed, 14 May 2025 21:02:19 +0200

### Bugfixes

- fix removal of cookies for certain domains when consent is revoked ([d33402098](https://github.com/marcantondahmen/automad/commit/d33402098960399f5a1de597270e23ec63ba3feb))

## [v2.0.0-alpha.34](https://github.com/marcantondahmen/automad/commit/f2ca0dc3d25cfea14a895f7008f353af97fa9cb0)

Sat, 10 May 2025 09:13:41 +0200

### New Features

- **consent**: add option to customize consent placeholder text ([c1b3fba3c](https://github.com/marcantondahmen/automad/commit/c1b3fba3cb50d537ca1caf3a01191e79a113b95c))
- **gallery**: improve masonry render engine precision ([27282edf2](https://github.com/marcantondahmen/automad/commit/27282edf2175405129b9f5173b82363944e26cfd))

### Bugfixes

- **consent**: fix consent banner border style ([0d33e167b](https://github.com/marcantondahmen/automad/commit/0d33e167b3b2af72a2fff022516d4039e78732ef))
- fix select inputs in safari ([8bb775f44](https://github.com/marcantondahmen/automad/commit/8bb775f442acc8297aa19e1efe003580717ed4fa))

## [v2.0.0-alpha.33](https://github.com/marcantondahmen/automad/commit/60b6ec6d92baf1b53d1746bb22db5ab443a49f91)

Thu, 8 May 2025 21:48:48 +0200

### New Features

- **consent**: add option to revoke cookie consent ([f73b235bf](https://github.com/marcantondahmen/automad/commit/f73b235bf8c42704962095ee44a044ba2279cc91))

### Bugfixes

- **consent**: fix breaking script tags when js content contains a greater-than symbol followed by letters ([2ccf78524](https://github.com/marcantondahmen/automad/commit/2ccf78524f2b417c16790c393b81315cdd11c849))

## [v2.0.0-alpha.32](https://github.com/marcantondahmen/automad/commit/9e0d721c135b900ffd410248ab92ceab0ed9e4bb)

Wed, 7 May 2025 22:43:47 +0200

### New Features

- add options to customize the cookie banner text, button labels and colors ([7fcbff95c](https://github.com/marcantondahmen/automad/commit/7fcbff95c7714022db64218c424295137a43a808))

### Bugfixes

- fix jumpbar link to community packages section ([70fcc98b1](https://github.com/marcantondahmen/automad/commit/70fcc98b137356a7f0b688935978017596e333dc))
- fix missing message when there are no search results ([3326a0afc](https://github.com/marcantondahmen/automad/commit/3326a0afcd6c2ecc761f885e02cb0009c5ac578f))
- only set composer auth environment variable when not being empty ([e1ebd8406](https://github.com/marcantondahmen/automad/commit/e1ebd840622489455b6cd1db9a1c44c8b7e153ed))
- prevent removal of packages that are dependencies of other packages ([02541a591](https://github.com/marcantondahmen/automad/commit/02541a591fdebacc5b357f212e84fbcd55ee6f7f))

## [v2.0.0-alpha.31](https://github.com/marcantondahmen/automad/commit/429432c6273489f0c9d4f832fdeef5d1cc28fba7)

Mon, 5 May 2025 19:36:37 +0200

### Bugfixes

- fix cookie consent banner styles ([76eac5d14](https://github.com/marcantondahmen/automad/commit/76eac5d149f704892e526b586984e7e52546aeb4))

## [v2.0.0-alpha.30](https://github.com/marcantondahmen/automad/commit/8d917d3cc8ceacdb3c6544cae95ba942a3d75991)

Sun, 4 May 2025 20:36:35 +0200

### Bugfixes

- fix package manager links in jumpbar dialog ([c0c716c1e](https://github.com/marcantondahmen/automad/commit/c0c716c1e922d82e064861a145395bf4e399996a))

## [v2.0.0-alpha.29](https://github.com/marcantondahmen/automad/commit/c5770192f8222f9f438aa90197772922c9c26c9d)

Sun, 4 May 2025 20:23:06 +0200

### Bugfixes

- fix error when fetching composer phar ([2df7c663c](https://github.com/marcantondahmen/automad/commit/2df7c663c8a0f04a97531eb65369e11c1c70e353))

## [v2.0.0-alpha.28](https://github.com/marcantondahmen/automad/commit/3af47b31360b3b43e8c0b2f38ad9c355f2154617)

Sun, 4 May 2025 20:12:16 +0200

### New Features

- add option to install private packages from github or gitlab using the dashboard ([c8f743248](https://github.com/marcantondahmen/automad/commit/c8f74324811ad6e71e45e126552ed9718cc4f5d9))
- automatically show cookie consent banner for embedded content ([99da3b1bf](https://github.com/marcantondahmen/automad/commit/99da3b1bf0125586317cd39762e6b60fc97086da))

### Bugfixes

- fix broken embed block services ([020ecde25](https://github.com/marcantondahmen/automad/commit/020ecde25c97d30a06c189a1d00208fc02a68a0b))
- fix empty debug log in api responses when debugging is disabled ([41fe627cf](https://github.com/marcantondahmen/automad/commit/41fe627cfae25d13b0ab911959c2ef607c9ddba4))
- fix twitter embed blocks ([0119fefb0](https://github.com/marcantondahmen/automad/commit/0119fefb0eb49e257052f64b1050fb458c42afe0))

## [v2.0.0-alpha.27](https://github.com/marcantondahmen/automad/commit/90f7f576f2bddee086ef1cbdcf6d2a2a123fef13)

Mon, 21 Apr 2025 20:38:50 +0200

### New Features

- add grid layout option for gallery blocks ([673fe8adb](https://github.com/marcantondahmen/automad/commit/673fe8adb0f3c437afbf5e9e99b4d99550afd7d5))
- improve spam protection and form validation of mail block ([255d6d5be](https://github.com/marcantondahmen/automad/commit/255d6d5be64e67b7a637c6e067eb275aea808ba2))

### Bugfixes

- fix backwards compatibility with PHP 8.2 ([27d5f54a6](https://github.com/marcantondahmen/automad/commit/27d5f54a6221d915fbc7223b761687380d4d6fea))
- fix row layout in gallery blocks ([2c45ae909](https://github.com/marcantondahmen/automad/commit/2c45ae9096bd01acd4b2883a979845b4b2cbbd99))

## [v2.0.0-alpha.26](https://github.com/marcantondahmen/automad/commit/de473357c8cf266a97a8daf104c5922c747eabb7)

Mon, 14 Apr 2025 21:53:30 +0200

### Bugfixes

- fix empty slideshow captions showing up on hover ([266783264](https://github.com/marcantondahmen/automad/commit/2667832641795d7c0835dd53b38c8f17ba41e02d))

## [v2.0.0-alpha.25](https://github.com/marcantondahmen/automad/commit/34d9c1dd4d1bea8223fba1b50cb4b14c11cbf000)

Sun, 13 Apr 2025 16:05:17 +0200

### New Features

- add i18n support for themes ([ce917910d](https://github.com/marcantondahmen/automad/commit/ce917910d98f1f9972f9aa296d3f93f906942cbf))
- add option to move files between pages ([f5738c4d1](https://github.com/marcantondahmen/automad/commit/f5738c4d1dcd621b3a542e53a303f0a2909dfe2c))
- add video block type ([db4ed58aa](https://github.com/marcantondahmen/automad/commit/db4ed58aa76de118548477a31cf06c1ea0a55cfc))
- show captions in slideshow blocks ([87b01bd73](https://github.com/marcantondahmen/automad/commit/87b01bd73fd2b832fdbb733cc19c43e76effa1c1))

### Bugfixes

- convert special characters inside meta tags to html entities ([2d142b9a8](https://github.com/marcantondahmen/automad/commit/2d142b9a8fd7f765b102962f6a605510fee9511f))
- fix broken image url when pasting image into shared content ([d78b2a031](https://github.com/marcantondahmen/automad/commit/d78b2a031a76ea7df14734105bd9e98dacc91d70))
- fix referrerpolicy for linked images ([5ef485f4e](https://github.com/marcantondahmen/automad/commit/5ef485f4ec8f19980448f79dae10adb9babf4be1))

## [v2.0.0-alpha.24](https://github.com/marcantondahmen/automad/commit/3718e8103c1bb2868a1e94137f9c44d65bc0d212)

Mon, 31 Mar 2025 19:11:08 +0200

### New Features

- **ui**: display block template files in human readable form ([4478818db](https://github.com/marcantondahmen/automad/commit/4478818db261a9533afee2754322ce81aefa61bd))
- add option to upload images inside the image picker modal ([83b58af5c](https://github.com/marcantondahmen/automad/commit/83b58af5c6a86ba079d3b65737d01643f43c06d8))
- add support for pasting and dragging images to the block editor ([9707ac2e4](https://github.com/marcantondahmen/automad/commit/9707ac2e48e534f985b8e78b5bacba041504e13c))
- bundle prism themes instead of using unpkg cdn ([19a2f81d6](https://github.com/marcantondahmen/automad/commit/19a2f81d641aaaf13313a6cfdb81778b07aa7870))

### Bugfixes

- display fields in the dashboard that are inside overwritten snippet includes ([7205533ee](https://github.com/marcantondahmen/automad/commit/7205533eef3caf3ccd76bb162ae4da95482c8aa1))
- fix resolving includes inside snippets that are defined in separate files ([12843fef0](https://github.com/marcantondahmen/automad/commit/12843fef019b4d8d524f603138cc2774b348ae21))

## [v2.0.0-alpha.23](https://github.com/marcantondahmen/automad/commit/569a77384abd28e4ef50fea71b00c998511377dd)

Mon, 24 Mar 2025 23:52:57 +0100

### New Features

- add new automad syntax theme ([b75336032](https://github.com/marcantondahmen/automad/commit/b753360329405d2f5849e135f7c36ccfa5014773))

## [v2.0.0-alpha.22](https://github.com/marcantondahmen/automad/commit/393a06788c7c978366e5c7e61bf7fab169b7d639)

Sun, 16 Mar 2025 13:15:06 +0100

### Bugfixes

- make composer installation for package manager more stable ([1beffd9bd](https://github.com/marcantondahmen/automad/commit/1beffd9bd4fbe63ed89718a4dcbb69e1e36916b5))

## [v2.0.0-alpha.21](https://github.com/marcantondahmen/automad/commit/7a15498c0005a8e86c83bca00052b806be511ef9)

Sun, 16 Feb 2025 15:08:43 +0100

### New Features

- bring back full error reporting when debugging is enabled ([0bdcc1b7d](https://github.com/marcantondahmen/automad/commit/0bdcc1b7dbec5040e9e1a4f0335ad6c896996a46))
- enable page caching also when get, post or session are not empty ([38cd4725e](https://github.com/marcantondahmen/automad/commit/38cd4725e66306805b819e0edc674519721ec422))

### Bugfixes

- fix navigating to the homepage from the dashboard in safari ([a6d809e1f](https://github.com/marcantondahmen/automad/commit/a6d809e1f4cdd945f731c712d2e802b2d1927789))

## [v2.0.0-alpha.20](https://github.com/marcantondahmen/automad/commit/098ebe45aaa2581f18a3437732a1f744d0d9efd6)

Mon, 10 Feb 2025 21:02:46 +0100

### Bugfixes

- fix infinite loop in breadcrumb api when editing pages with secondary language in dashboard and language routing is enabled ([cf44ff921](https://github.com/marcantondahmen/automad/commit/cf44ff921f9cc9f85c17fe7778ac79c54fec62c2))

## [v2.0.0-alpha.19](https://github.com/marcantondahmen/automad/commit/a5c13210580525ed730c5bbbd7a857a90fbb2b74)

Sun, 9 Feb 2025 13:23:36 +0100

### New Features

- allow certain configuration settings to be defined as environment variables ([1b68cbbd1](https://github.com/marcantondahmen/automad/commit/1b68cbbd1407be158b25f55304555e5d7dfdc697))
- include actual configuration and server environment in json log files ([268cd8754](https://github.com/marcantondahmen/automad/commit/268cd87543cf70f86de9d86c29c6b636e4a160a2))

## [v2.0.0-alpha.18](https://github.com/marcantondahmen/automad/commit/029e285078186a32457af925769a2338de641372)

Sat, 8 Feb 2025 14:22:11 +0100

### New Features

- add configuration option to enable open_basedir restriction ([4d067f7ea](https://github.com/marcantondahmen/automad/commit/4d067f7ea6b0fc5e7e8a862f3815c5af33e2dd37))
- add option to add account details when creating users with the console ([bafa155ea](https://github.com/marcantondahmen/automad/commit/bafa155eaad021f2f6656f6deb0928837ba58585))
- add option to hide caching, debug and mail settings from users ([824ebc7f5](https://github.com/marcantondahmen/automad/commit/824ebc7f597893734a255940bf2d1bf9f12ec442))
- add option to put server in maintenance mode and make content read-only ([1cf051b9c](https://github.com/marcantondahmen/automad/commit/1cf051b9c8f2a6164ccfa3f6c82d52746e634c54))
- improve console and add config commands ([3cebd17f6](https://github.com/marcantondahmen/automad/commit/3cebd17f6620972d3f4ef976ae9b63d56c60d924))

## [v2.0.0-alpha.17](https://github.com/marcantondahmen/automad/commit/c9476f22fe75fbebdb2b0bceaf7f6336bc0ae683)

Sat, 1 Feb 2025 16:57:15 +0100

### New Features

- add reusable component block type ([254a62a2d](https://github.com/marcantondahmen/automad/commit/254a62a2d5816f65befd13906ec2b0ed72861846))
- add support for php 8.4 ([a8ff6ce06](https://github.com/marcantondahmen/automad/commit/a8ff6ce06899335b7779dc7313f552207f8ba460))
- ignore existing but empty path info server var when resolving request ([38315cd0a](https://github.com/marcantondahmen/automad/commit/38315cd0ade9560420b1d39d0207515221e1c06e))
- improve mail error messages ([639d35070](https://github.com/marcantondahmen/automad/commit/639d3507061cfd566e85c31e70c6cf1aaaead665))
- improve open-graph image style ([61df95c02](https://github.com/marcantondahmen/automad/commit/61df95c02fc761a04961940dcdde0c3a221d957f))
- replace urlify package with slugify ([dbdb3b581](https://github.com/marcantondahmen/automad/commit/dbdb3b5814d185b2516ef088f60d54985dc25290))
- revert merging localized homepage data with shared data ([5523abebe](https://github.com/marcantondahmen/automad/commit/5523abebe53f2704d089ef88deb03aa7117a2439))
- show component block content as read-only preview in editor ([174f9e2b9](https://github.com/marcantondahmen/automad/commit/174f9e2b97eb9ad900cc5ed010519f957067c540))
- use configured email for from field and visitor email for reply-to field ([10b3ef01d](https://github.com/marcantondahmen/automad/commit/10b3ef01d393c1c3df92091e21af3bcccc7fe43f))

### Bugfixes

- fix language routing for language codes with more than two characters ([dde411dee](https://github.com/marcantondahmen/automad/commit/dde411deec6c9acee3fd18be21f822a4ab0d2138))

## [v2.0.0-alpha.16](https://github.com/marcantondahmen/automad/commit/53cab745c3b45291f0c8a3e450543d10a38e9d98)

Sat, 14 Dec 2024 22:58:39 +0100

### New Features

- add charset meta tag to rendered pages automatically ([82512bcb2](https://github.com/marcantondahmen/automad/commit/82512bcb26dd269653507f55cda2d14a9badaf74))
- update standard theme to version 1.0.7 ([b78efaf24](https://github.com/marcantondahmen/automad/commit/b78efaf24748dae0d5ffe843902d788b01d3f0c8))

### Bugfixes

- fix layout section defaults ([91b0b0160](https://github.com/marcantondahmen/automad/commit/91b0b0160b5336cf409083ed851e9e07bee92f08))
- fix missing cache directory error when saving open-graph images ([f0b2f2f5c](https://github.com/marcantondahmen/automad/commit/f0b2f2f5c8ba8b302e1aee630b00b438f1027e50))
- fix missing email alter position in dom ([b2c09b01a](https://github.com/marcantondahmen/automad/commit/b2c09b01a141626adf4c1bcae82cee48eb49eb27))
- fix section block spacing ([4c0fd4654](https://github.com/marcantondahmen/automad/commit/4c0fd465409435dbb6d00909dbcff7a5eb2cdeff))
- fix tests ([1a19b1337](https://github.com/marcantondahmen/automad/commit/1a19b133719727f08bf04e35a66c49a95a97d581))
- fix text wrapping in code editor ([e190c5def](https://github.com/marcantondahmen/automad/commit/e190c5def5ef9de4c67f8cb461e83020fc495385))
- strip tags from meta tag content ([39551032f](https://github.com/marcantondahmen/automad/commit/39551032f9afb6514c3e5c9e5f79f6a2225a2245))

## [v2.0.0-alpha.15](https://github.com/marcantondahmen/automad/commit/5a52e1f46c54db96395f6dbe25ff9dd11af7c643)

Fri, 13 Dec 2024 18:00:44 +0100

### Bugfixes

- refactor build script ([fbfbc7058](https://github.com/marcantondahmen/automad/commit/fbfbc70581742ea47055fb1a287d43355b6c5e69))

## [v2.0.0-alpha.14](https://github.com/marcantondahmen/automad/commit/5431d379fb67c94a30eb1a0a4f50a9b7753ad4ab)

Fri, 13 Dec 2024 17:51:44 +0100

### Bugfixes

- fix bundle issues ([a9785e611](https://github.com/marcantondahmen/automad/commit/a9785e611e600c93690cbb435fe8c293891e11b3))

## [v2.0.0-alpha.13](https://github.com/marcantondahmen/automad/commit/53a594f06fbc7d169684399b9731a7f34b10a797)

Fri, 13 Dec 2024 17:34:21 +0100

### Bugfixes

- fix dist bundle issues ([516cfef45](https://github.com/marcantondahmen/automad/commit/516cfef458a2abc04243e113b948097972b72357))

## [v2.0.0-alpha.12](https://github.com/marcantondahmen/automad/commit/810d7bc99b06e09e36412fec3ecf22ea8052614c)

Fri, 13 Dec 2024 16:56:06 +0100

### Bugfixes

- fix missing fonts in dist bundle ([73e34b55e](https://github.com/marcantondahmen/automad/commit/73e34b55e8a8b646141cd84ccdc796afb29242f7))

## [v2.0.0-alpha.11](https://github.com/marcantondahmen/automad/commit/61449346db3c0b70109892daa2f987e3a913b2de)

Fri, 13 Dec 2024 16:10:26 +0100

### New Features

- add automatically generated open-graph image and tags when rendering pages ([fbdc8ac79](https://github.com/marcantondahmen/automad/commit/fbdc8ac79a1a68e674390a35c24969746216242a))
- add better notifications whenever a user has no associated email address ([f99bcc808](https://github.com/marcantondahmen/automad/commit/f99bcc8087cafb7461e2d17053e4a2eada6f8096))
- add number field type ([fae969487](https://github.com/marcantondahmen/automad/commit/fae969487d512787b3222b2db1268a9f7920e1d1))
- add support for custom logo in open-graph images ([7675b0905](https://github.com/marcantondahmen/automad/commit/7675b09050bc15b49db66ad458ed9de102d6d348))
- automatically add favicons and apple touch icon when existing ([b5bbd5439](https://github.com/marcantondahmen/automad/commit/b5bbd54399158bd03d256cf1e8699d3212aafda7))
- make prev and next selections work with language router and disable looping ([37fc8bf8b](https://github.com/marcantondahmen/automad/commit/37fc8bf8bd430cc95bf7e7b167859d189d871006))
- set title for 404 pages ([df3ca2b15](https://github.com/marcantondahmen/automad/commit/df3ca2b154e4e90e28fabf8146f2e4165e85a210))

### Bugfixes

- **ui**: fix image links not being saved when using the autocomplete form ([dcbd537d9](https://github.com/marcantondahmen/automad/commit/dcbd537d916741bfb3aec330e1d777825828bb2c))
- **ui**: fix page reload when page tags are edited ([01db0d55e](https://github.com/marcantondahmen/automad/commit/01db0d55e8371f11e717dcfe09460f79d07d39a4))
- disable in-page editing on 404 pages ([c39522113](https://github.com/marcantondahmen/automad/commit/c3952211318e473cf2002427e430efe744f77596))
- fix breadcrumbs selection on multilingual sites ([390bfbb1c](https://github.com/marcantondahmen/automad/commit/390bfbb1c6d6d8c5a805de2a4c53ec2e4bb79e97))
- fix error in disk usage calculation when following broken symlinks ([07c360064](https://github.com/marcantondahmen/automad/commit/07c3600642c164f9b07909303f3af42556c2a990))
- fix finding first image in rendered blocks ([06ce168da](https://github.com/marcantondahmen/automad/commit/06ce168dae537c2b0a12b8d8690870b3e335cd9f))
- fix warning when purging cache across file systems ([e4a3a92ef](https://github.com/marcantondahmen/automad/commit/e4a3a92efcdb94d22809ede640ec46cd4db90d2a))

## [v2.0.0-alpha.10](https://github.com/marcantondahmen/automad/commit/cc8976c207bcc937e18dab442a66f6bd10289ebd)

Sun, 24 Nov 2024 12:01:23 +0100

### New Features

- add download link to file cards ([461ece5e4](https://github.com/marcantondahmen/automad/commit/461ece5e4a39e3d038c56fbdc69d00129e9fcec9))
- add option to define the order of fields inside the dashboard in the theme.json file ([55d93ec1a](https://github.com/marcantondahmen/automad/commit/55d93ec1a0164c6c7ba51bcd88488e6c8bac0350))
- make sure that block ids are always unique in rendered html ([9f07136e8](https://github.com/marcantondahmen/automad/commit/9f07136e84fce6fc7306b57f479b5f625703e60c))

### Bugfixes

- **ui**: fix button text overflow breaking card layouts ([2bdfe5fa3](https://github.com/marcantondahmen/automad/commit/2bdfe5fa30b8876812b9c2494233df4109aadc21))
- **ui**: fix datetime input field style on ios ([166e1dc7b](https://github.com/marcantondahmen/automad/commit/166e1dc7b434bf41fbdc5c3ce0f6a5ac74f224f5))
- fix download links in file info modal ([0b81320a4](https://github.com/marcantondahmen/automad/commit/0b81320a4b774e7055d1de5d0b5df5a27437d88d))
- fix navbar spacing in inpage edit form ([0fd31f749](https://github.com/marcantondahmen/automad/commit/0fd31f749ffccf14be8377d69db167dc75ae539f))
- fix position of inpage edit overlay in dom ([486a455ed](https://github.com/marcantondahmen/automad/commit/486a455ed3688fb0b5195c9307136a71c4889c8b))
- fix processing email addresses inside of markdown blocks ([af569f032](https://github.com/marcantondahmen/automad/commit/af569f032ff0ef47eef5a073042c4ace06ed2c26))
- fix small display alert being copied with editor content ([cb7a6c73a](https://github.com/marcantondahmen/automad/commit/cb7a6c73a387d12b126f2547361120fc1d42a58c))
- fix template name display in page data form ([451101ded](https://github.com/marcantondahmen/automad/commit/451101ded3cb4aa8b3e35467e300882b6d2f31b8))

## [v2.0.0-alpha.9](https://github.com/marcantondahmen/automad/commit/c6118ae98545f0d2e2783f2894d1a6da00d57121)

Sat, 9 Nov 2024 19:24:22 +0100

### New Features

- **ui**: add undo and redo button tooltips ([e5def55b6](https://github.com/marcantondahmen/automad/commit/e5def55b62bb13608cea0dfe671034ec32caff00))
- **ui**: improve highlighting of matches in search results ([4586f54bb](https://github.com/marcantondahmen/automad/commit/4586f54bbd3d65a764f0fb8fd35d8925cef212d6))
- add option to discard changes and revert content to the last published version ([2a40c1e74](https://github.com/marcantondahmen/automad/commit/2a40c1e74ef6fdf1301252774fb608595db104ea))
- add public pagelist api ([6967a5faa](https://github.com/marcantondahmen/automad/commit/6967a5faa2c2413495695a00231e0c799a543cf3))
- add select field type ([45a7e1569](https://github.com/marcantondahmen/automad/commit/45a7e156999873a494b9fb91eac05bfb99957e4f))
- improve handling of usernames and emails ([847b80aab](https://github.com/marcantondahmen/automad/commit/847b80aabc87cb7eeb4ca851de958bd63a26541c))
- purge unused fields when changing page templates ([1a23b56c4](https://github.com/marcantondahmen/automad/commit/1a23b56c42ed311d4fe16d99ebe74be5f6d1f66c))
- purge unused fields when changing the main theme ([b9f1da599](https://github.com/marcantondahmen/automad/commit/b9f1da599c93b3cafd8db820e2d78c0a92711ad9))

### Bugfixes

- **ui**: fix several styling issues ([3e0641ad8](https://github.com/marcantondahmen/automad/commit/3e0641ad813a34d74d14cb9973e60d3db62d2e1b))
- fix image editor button and modal window style issues ([2db554413](https://github.com/marcantondahmen/automad/commit/2db554413353aacbc39bdfc701175b1c832bda24))
- fix percentage unit input field ([b6df22c5f](https://github.com/marcantondahmen/automad/commit/b6df22c5f454213fe2ff727859802428824aeb52))

## [v2.0.0-alpha.8](https://github.com/marcantondahmen/automad/commit/80de5b2483592d4a94a034292b560bbf7298de43)

Sun, 27 Oct 2024 19:34:06 +0100

### New Features

- add customization editor placeholders with shared values ([8aa92e3c6](https://github.com/marcantondahmen/automad/commit/8aa92e3c631f1f43fe7d3eecd18f9db1bea55962))
- hide block editor on small devices and show alert instead ([2b3f644ee](https://github.com/marcantondahmen/automad/commit/2b3f644ee2a3067b87d9170ac07b25c9cd62164f))
- improve customization field labels and add customization section info ([a08ce16df](https://github.com/marcantondahmen/automad/commit/a08ce16dff3a30225d0c01f080aa3789459a89f0))
- improve urls of remote images ([ab6514de1](https://github.com/marcantondahmen/automad/commit/ab6514de1dfd6d434aec7149aaf3e116b319bfde))
- merge data of localized home page with shared data when language router is enabled ([81165de58](https://github.com/marcantondahmen/automad/commit/81165de58b76a46ff2a4d903ef2fa15f710bc503))
- replace custom js and css file fields with custom html fields ([38be7bab4](https://github.com/marcantondahmen/automad/commit/38be7bab48f0825abbc36903dc90564eedfc968d))

### Bugfixes

- **i18n**: fix home page url when language router is enabled ([02391e2f1](https://github.com/marcantondahmen/automad/commit/02391e2f1df24b808e64778a0fdcd7edb1f57f8d))
- **ui**: fix disappearing block when dragging onto itself ([d3a3d4a9b](https://github.com/marcantondahmen/automad/commit/d3a3d4a9b3b25ce70ab2c69337d85c87b379abc4))
- **ui**: fix hidden dropdown items on small devices ([0800a93dc](https://github.com/marcantondahmen/automad/commit/0800a93dcb23c8cca07f753200c4d1d163711e80))
- **ui**: fix text overflow in jump bar ([03109a60c](https://github.com/marcantondahmen/automad/commit/03109a60c4ca18a2c00c4032cabe935861d279e1))
- **ui**: fix text overflow of autocomplete dropdowns ([45cd0b25d](https://github.com/marcantondahmen/automad/commit/45cd0b25d2383170d8e2cc3d85b58401b5b18b67))
- enable text wrap in code editors in order to fix invisble text ([fa107781c](https://github.com/marcantondahmen/automad/commit/fa107781caf211191979da38149844ae9246df5f))
- fix deleting image caption not triggering change event ([1960fb0be](https://github.com/marcantondahmen/automad/commit/1960fb0be7a30632cb15e247d93a18ae5b23a6a3))

## [v2.0.0-alpha.7](https://github.com/marcantondahmen/automad/commit/774d20584dd470555a61b3b2afd03261bf96e8f5)

Sun, 13 Oct 2024 20:56:43 +0200

### New Features

- add disk usage monitoring ([bd6495852](https://github.com/marcantondahmen/automad/commit/bd64958529e5fbb7251c7b77ff189310af9593bd))
- add option to define a disk quota ([de39719aa](https://github.com/marcantondahmen/automad/commit/de39719aacb7e84e5ec6377e4cc37cf96b8c9838))
- add option to filter the list of allowed packages ([b525156b2](https://github.com/marcantondahmen/automad/commit/b525156b2d49b327e70188ae1c6145904d57a7bc))
- deduplicate form submissions ([a6ded4e63](https://github.com/marcantondahmen/automad/commit/a6ded4e639f1f0b6dddda26e172891e5ae84b0c6))
- improve naming of resized images in cache ([075b45fec](https://github.com/marcantondahmen/automad/commit/075b45fec5a02cc56b1eb12964d8f9d6a591b095))

### Bugfixes

- **blocks**: fix pasting and merging blocks ([c811131f9](https://github.com/marcantondahmen/automad/commit/c811131f9da6edb333f51c3495f46829be044739))
- **ui**: fix visibility of sidebar items on medium size devices ([de2e6fe40](https://github.com/marcantondahmen/automad/commit/de2e6fe402b2ce2801a2ba4bd632c38d16640fe7))
- fix dashboard redirections when saving pages the are aliases for the home page ([4cb37fd55](https://github.com/marcantondahmen/automad/commit/4cb37fd55757558c8666eb2886e08952ed0e7c6a))
- fix wrong file overwriting errors when editing page and shared data ([1e4903f09](https://github.com/marcantondahmen/automad/commit/1e4903f09af54c900f876da84a60d3bcca6a44eb))

## [v2.0.0-alpha.6](https://github.com/marcantondahmen/automad/commit/6c607392b3fe02b202790bd496e109089525367b)

Sun, 15 Sep 2024 19:37:00 +0200

### New Features

- **ui**: improve button loading animation ([7fbb813ef](https://github.com/marcantondahmen/automad/commit/7fbb813ef89b19da0b16662764da3fa914a9aa7c))
- add customization fields for CSS and JS code and files ([660691c24](https://github.com/marcantondahmen/automad/commit/660691c2459c33750436a57e50ad7d8237b18e47))
- add support for remote webp images ([0f1885dd9](https://github.com/marcantondahmen/automad/commit/0f1885dd9d1236549d45b90a936e98ed24f39fab))
- move mail config to a separate file ([b48b25329](https://github.com/marcantondahmen/automad/commit/b48b253292e950dcbd74d8bd81f898e9933c31c2))

### Bugfixes

- **ui**: fix visibility of navbar items on medium size screens ([2f3258822](https://github.com/marcantondahmen/automad/commit/2f32588222c4829ca3fad8009d9093949f48360d))
- fix processing of nested in-page editing buttons ([4fa5eb0d3](https://github.com/marcantondahmen/automad/commit/4fa5eb0d3cad40ee39338c975eb427b0c68c6674))

## [v2.0.0-alpha.5](https://github.com/marcantondahmen/automad/commit/09e8864bdc5a62ba735aa0a7f08d358e92aaa735)

Sun, 30 Jun 2024 19:41:20 +0200

### New Features

- add better button loading state ([f2eaf3696](https://github.com/marcantondahmen/automad/commit/f2eaf369649fc0c311c2cb8b416031d26dd1a074))

### Bugfixes

- **history**: fix missing revisions when using in-page edit mode ([4a87da80c](https://github.com/marcantondahmen/automad/commit/4a87da80c22aaecde932ee92ec458b49aaf40db7))
- fix delete button state for empty file lists ([c9617f0ac](https://github.com/marcantondahmen/automad/commit/c9617f0ac080b94124bc2b7baae78f4c9d70d2ab))
- fix image previews when pretty urls are disabled ([2cc2afa80](https://github.com/marcantondahmen/automad/commit/2cc2afa806898b168e48e1657400b049950c9f5f))
- fix remote code execution vulnerability in file editor ([85e23bc67](https://github.com/marcantondahmen/automad/commit/85e23bc67aadb84df633c41ee782cd106e1413da))
- set proper response code when uploading unsupported file type ([66f369c30](https://github.com/marcantondahmen/automad/commit/66f369c30f015db819206a8459c0a131cfcd8f7d))

## [v2.0.0-alpha.4](https://github.com/marcantondahmen/automad/commit/ff40d45a283385b05eac7e5f2bea3936fe578d29)

Sat, 13 Apr 2024 00:13:36 +0200

### New Features

- **engine**: add syntax highlighting for code blocks and markdown variables ([e9d14ae1d](https://github.com/marcantondahmen/automad/commit/e9d14ae1dcb2fce4efdd22b262ecc923364fccaa))
- **migration**: map legacy standard theme to new standard-v1 composer package ([94b3714fb](https://github.com/marcantondahmen/automad/commit/94b3714fb6a8b56a837cd653a20087ef8b669e48))
- **ui**: add syntax highlighting theme selection field to page and shared settings ([cf85d6ae8](https://github.com/marcantondahmen/automad/commit/cf85d6ae854bf0f0d2ed400fc388589ff02dc524))
- add line numbers and copy button for code blocks ([ca6c70014](https://github.com/marcantondahmen/automad/commit/ca6c70014ba037a9efcd8cff9dd55a696a2a6f3a))
- add syntax highlighting for the automad template language ([94aefa355](https://github.com/marcantondahmen/automad/commit/94aefa355e6f150f0a4078c939a51aa8aa11c973))

### Bugfixes

- **blocks**: fix gallery counter and caption color ([9049b750e](https://github.com/marcantondahmen/automad/commit/9049b750e6d281aa687d60b95a79c611255f46a1))
- **core**: set cookie path to base url ([118bd1642](https://github.com/marcantondahmen/automad/commit/118bd16423030b00dbf295b4ff8185d75af15cf5))
- **dashboard**: fix image editor color select and modal styles ([aca720b55](https://github.com/marcantondahmen/automad/commit/aca720b5531cd15defa9f80106a8658251548c8a))
- **dashboard**: fix missing publish button on small devices ([8678ab32e](https://github.com/marcantondahmen/automad/commit/8678ab32e66fc2e5e551e899da3c8317198e2f88))
- **dashboard**: fix text selection colors ([1d7e3203e](https://github.com/marcantondahmen/automad/commit/1d7e3203e24a92981d3f0289cb08f539be254abf))
- **dashboard**: open in-page edit mode in same tab ([56e89b839](https://github.com/marcantondahmen/automad/commit/56e89b839c9f01271ca3fc957d72a01ee010c521))
- **engine**: fix missing strikethrough support in markdown fields ([f700a1be7](https://github.com/marcantondahmen/automad/commit/f700a1be79c8f048b8ebee137c8b11b5e14593fc))

## [v2.0.0-alpha.3](https://github.com/marcantondahmen/automad/commit/7f5ae3584e30a3c4e9c8d3430bf52a7c24e55a5a)

Thu, 29 Feb 2024 21:03:22 +0100

### New Features

- **console**: add migrate command for the migration of version 1 sites ([7403a58c8](https://github.com/marcantondahmen/automad/commit/7403a58c8ede73b18b960191f408926901a2f7ad))

### Bugfixes

- fix syntax highlighting colors ([d40291c37](https://github.com/marcantondahmen/automad/commit/d40291c376fdfd36fcec8961927d28148d3475ea))

## [v2.0.0-alpha.2](https://github.com/marcantondahmen/automad/commit/0b38cec48ca28bd6e6c78f9686e612923fcaefe0)

Sat, 24 Feb 2024 22:43:44 +0100

### New Features

- add option to install Automad manually ([6f0ee3a96](https://github.com/marcantondahmen/automad/commit/6f0ee3a96542fb26de6816430938ba2c5283f1df))

### Bugfixes

- fix homepage links for empty base urls ([22f35c4a5](https://github.com/marcantondahmen/automad/commit/22f35c4a5ac88bcfa55c5e254061ff8e59a22423))

## [v2.0.0-alpha.1](https://github.com/marcantondahmen/automad/commit/46ba17b955e9905c79a07b3673af40f1c4c30f71)

Mon, 19 Feb 2024 19:44:14 +0100

### Breaking Changes

- **ui**: pages can be sorted and moved directly in the sidebar ([4579f83e1](https://github.com/marcantondahmen/automad/commit/4579f83e1d4bc5affb5dde7dc3a506c7e45e140f))
- change minimum required PHP version to 8.2 ([5efeb8e](https://github.com/marcantondahmen/automad/commit/5efeb8eb083544f7c437ea8c0540e2ea2895f0e6))
- remove headless mode feature ([c79b377c8](https://github.com/marcantondahmen/automad/commit/c79b377c8e1dd2a815f46e7728aaa9ab7cce025b))

### New Features

- **blocks**: add align items layout option for section blocks ([f7b7bed25](https://github.com/marcantondahmen/automad/commit/f7b7bed25119968cd8a0e67e60d633dc0052f3ae))
- **blocks**: add block duplication tune ([e52be1a57](https://github.com/marcantondahmen/automad/commit/e52be1a57ee0e64a455880a1f07a7ebedb4ee92a))
- **core**: add debug logging to json files ([fa2821212](https://github.com/marcantondahmen/automad/commit/fa2821212b1f77ef0927eb7292ffec3329d10481))
- **core**: add draft and published states for pages and shared content ([d1b539d57](https://github.com/marcantondahmen/automad/commit/d1b539d5754f8438ef854a1a2c3b3564edfb886e))
- **core**: add i18n support for multilingual sites ([b3f0bb505](https://github.com/marcantondahmen/automad/commit/b3f0bb505c71907179d920002ce47cf388ed8cf1))
- **core**: add support for webp images ([19cc81574](https://github.com/marcantondahmen/automad/commit/19cc81574c48c8b6b4560e1acd1c78383a8f09a2))
- **dashboard**: store translations in json files ([f3deae501](https://github.com/marcantondahmen/automad/commit/f3deae5010b53f38c75d78dd008321d0394ca508))
- **ui**: add FileRobot image editor ([4bcf9bd52](https://github.com/marcantondahmen/automad/commit/4bcf9bd52bad5eb11eb859d99a329099cfcdbef3))
- **ui**: add dark mode for dashboard ([da343b8f9](https://github.com/marcantondahmen/automad/commit/da343b8f9011c06db72e861bf723363c954e1d85))
- **ui**: add field and file filter ([2ecf1c0b4](https://github.com/marcantondahmen/automad/commit/2ecf1c0b4a9a7173b3ccfdbe05d02222ed38b841))
- **ui**: add password requirements check ([83e097103](https://github.com/marcantondahmen/automad/commit/83e097103b3cc0ac70a99e55fdb715d45ef75a3f))
- **ui**: implement undo functionality ([053a9c3f2](https://github.com/marcantondahmen/automad/commit/053a9c3f2c54e8a494a08e79011cc4a4cfd00f4b))
- **ui**: pages can be sorted and moved directly in the sidebar ([4579f83e1](https://github.com/marcantondahmen/automad/commit/4579f83e1d4bc5affb5dde7dc3a506c7e45e140f))
- **ui**: remove UIkit from Composer dependencies ([93a2bb7ee](https://github.com/marcantondahmen/automad/commit/93a2bb7ee0637dd6392e1f94fe076f49d0f88eca))
- **ui**: remove jQuery from NPM dependencies ([989b21fe5](https://github.com/marcantondahmen/automad/commit/989b21fe598fcc24d3f8383a21d2410559cebf02))
- add ability to restore deleted pages ([c4067bac3](https://github.com/marcantondahmen/automad/commit/c4067bac3fb74ebecdb99e9dddce43495185e7a6))
- add result context when searching for pages ([63e838e88](https://github.com/marcantondahmen/automad/commit/63e838e88957ad47d98f66a0a05c6bb6c015a45d))
- add version history for pages ([2b139fba7](https://github.com/marcantondahmen/automad/commit/2b139fba7912608377e8626a5197a05c4d26d404))
- allow for running Automad behind a proxy ([414dd5aa2](https://github.com/marcantondahmen/automad/commit/414dd5aa24df1427fccd0ff7d722b3a528a57cd6))
- remove headless mode feature ([c79b377c8](https://github.com/marcantondahmen/automad/commit/c79b377c8e1dd2a815f46e7728aaa9ab7cce025b))
- use .json instead of .txt files to store page and shared data ([96a1f184a](https://github.com/marcantondahmen/automad/commit/96a1f184ab0435c82a1a382a8c3b8de5e3a2751d))
- use symfony/mailer for sending emails ([5efeb8e](https://github.com/marcantondahmen/automad/commit/5efeb8eb083544f7c437ea8c0540e2ea2895f0e6))

## [v1.10.9](https://github.com/marcantondahmen/automad/commit/776a2d12b817bdc4eb29dbade1fc3039c9dc8b9d)

Tue, 19 Apr 2022 10:24:13 +0200

### Bugfixes

- **engine**: fix type error caused by pipe extensions that may return null ([19ec92256](https://github.com/marcantondahmen/automad/commit/19ec92256bf5d02e15065af1ddb818c3117fec56))

## [v1.10.8](https://github.com/marcantondahmen/automad/commit/7e4ed8f52aa6cdb8c068e53f8bb2a1335a45be56)

Thu, 7 Apr 2022 20:00:07 +0200

### Bugfixes

- **ui**: fix installation error ([ccd4e841d](https://github.com/marcantondahmen/automad/commit/ccd4e841d165e602d3363645c9de71f9d502f952))

## [v1.10.7](https://github.com/marcantondahmen/automad/commit/b402f7c7ff18f56f418fa196a128884f5c076b57)

Sat, 2 Apr 2022 23:53:44 +0200

### Bugfixes

- **cli**: fix cli updates ([284afb0bd](https://github.com/marcantondahmen/automad/commit/284afb0bdbce370b5bc096a19316a0851ca679b6))

## [v1.10.6](https://github.com/marcantondahmen/automad/commit/bbad3ee2100d82f66444ca5f28ad5321895b1dcd)

Sat, 2 Apr 2022 23:36:28 +0200

### Bugfixes

- **console**: fix console command error ([3e3682a67](https://github.com/marcantondahmen/automad/commit/3e3682a678bc395ce898b4eb8d88867a7994d8c3))
- fix PHP 8.1 compatibility issues ([1c2a755da](https://github.com/marcantondahmen/automad/commit/1c2a755da0d83285bb98c017a818affc031104ab))

## [v1.10.5](https://github.com/marcantondahmen/automad/commit/19a36ea95e3b412db1e2ce85f7b357be2e836160)

Sat, 2 Apr 2022 21:48:39 +0200

### Bugfixes

- **ui**: fix messenger error when returning null ([ae1a51d6a](https://github.com/marcantondahmen/automad/commit/ae1a51d6ab5489d8dd37f31c287f4bdda8f59c94))

## [v1.10.4](https://github.com/marcantondahmen/automad/commit/0255fad39ac3a534a97164521b423839fa82a2bb)

Sat, 2 Apr 2022 21:36:15 +0200

### Breaking Changes

- **core**: replace strftime() with IntlDateFormatter instance ([3428c8456](https://github.com/marcantondahmen/automad/commit/3428c8456d9ea5092e6e83f7cb9ac70bd881cf49))

### Bugfixes

- **core**: fix overriding config for tests ([8b9e4b3ed](https://github.com/marcantondahmen/automad/commit/8b9e4b3ed51f2c3f0c138ed989a67e9f5d54a3f8))
- **core**: replace strftime() with IntlDateFormatter instance ([3428c8456](https://github.com/marcantondahmen/automad/commit/3428c8456d9ea5092e6e83f7cb9ac70bd881cf49))
- fix passing null as non-nullable function parameters ([3822317d3](https://github.com/marcantondahmen/automad/commit/3822317d304dc7e4dfe80e42c734e8309658a5d7))

## [v1.10.3](https://github.com/marcantondahmen/automad/commit/f65ff67cce39173cea76ca3165f2b9c9deba8b59)

Fri, 1 Apr 2022 16:42:24 +0200

### Bugfixes

- **core**: fix deprecation warning for preg_split() ([87dd50597](https://github.com/marcantondahmen/automad/commit/87dd5059720805f7ad386c696977d40dede71b3e))
- **core**: fix php 8.1 compatibility issues with trim() and strip_tags() ([5dee18a21](https://github.com/marcantondahmen/automad/commit/5dee18a21ca85af24457a4a0ee22cdee8201101b))

## [v1.10.2](https://github.com/marcantondahmen/automad/commit/4d7f852678919babc453c8d65f801a8b7c106f58)

Thu, 31 Mar 2022 18:52:37 +0200

### Bugfixes

- **system**: fix fetching packages from the packagist api ([8b53ae63d](https://github.com/marcantondahmen/automad/commit/8b53ae63d4e32343cc1f428259ec4bd719cccc84))

## [v1.10.1](https://github.com/marcantondahmen/automad/commit/b538f5eb6ab097452c2b7d8498417ebe39d2a861)

Fri, 26 Nov 2021 10:26:38 +0100

### New Features

- **system**: add optional AM_SERVER constant in order to allow customizations of URLs generated by Server::url() ([ea70b0728](https://github.com/marcantondahmen/automad/commit/ea70b07287f64255f119eb666a523e987219d07e))

### Bugfixes

- **ui**: fix block editor ui errors when using block templates ([feb09bc2d](https://github.com/marcantondahmen/automad/commit/feb09bc2d6b998d4f1b062c028f1ae273042720b))

## [v1.10.0](https://github.com/marcantondahmen/automad/commit/f631730deb600db7ee9086954943e6b111405f49)

Wed, 24 Nov 2021 09:35:46 +0100

### New Features

- **core**: add feed generator ([0f2d32ace](https://github.com/marcantondahmen/automad/commit/0f2d32acede3c72ef468034dd3113f3ffad25da6))
- **ui**: add feed configuration ui ([686d563d1](https://github.com/marcantondahmen/automad/commit/686d563d11a8cdec881c6053cc01ad6049e76400))
- **ui**: redesign system settings ui ([aa84f8021](https://github.com/marcantondahmen/automad/commit/aa84f8021a0da6ff8d587e5f384cf6b6a46d1b4d))
- remove gulp-util and update dependencies ([02ae09d59](https://github.com/marcantondahmen/automad/commit/02ae09d59931fc09f92fb8fd0161147d199031c4))

## [v1.9.4](https://github.com/marcantondahmen/automad/commit/e52d003777a26e28746ab50140a0ebb65f0a28e5)

Thu, 11 Nov 2021 12:06:24 +0100

### New Features

- **themes**: improve color scheme of dark theme ([11c27250f](https://github.com/marcantondahmen/automad/commit/11c27250f46b2ff9b8438289096e48a6b48f4aec))
- **ui**: allow uppercase letters in custom field names ([0d11fc032](https://github.com/marcantondahmen/automad/commit/0d11fc032d0082a01190c8ed1cf020fdb31445fd))

### Bugfixes

- **core**: fix :current and :currentPath properties of pages that are redirected ([f79a2cfa9](https://github.com/marcantondahmen/automad/commit/f79a2cfa9907334f4615a5dadda09ab7c4fa0635))
- **ui**: fix overflowing select buttons ([78c3f9a7d](https://github.com/marcantondahmen/automad/commit/78c3f9a7d746422ec9a3e3ae1597b3e96bc5054f))
- **ui**: fix overflowing toolbox ([bad19924b](https://github.com/marcantondahmen/automad/commit/bad19924b32a58aa65248844fa52b7b0d2df6b23))

## [v1.9.3](https://github.com/marcantondahmen/automad/commit/ad5287079c87a7ad90835e731f78ef3e0ecc64b5)

Fri, 15 Oct 2021 23:20:45 +0200

### New Features

- **engine**: register snippet definitions that are nested within overrides ([e6ad71f65](https://github.com/marcantondahmen/automad/commit/e6ad71f659437ac33c947f48c0225ee749d23a9c))

## [v1.9.2](https://github.com/marcantondahmen/automad/commit/dec961145927e46d4a221cb85e30a12a29b3d6b0)

Fri, 15 Oct 2021 22:08:49 +0200

### New Features

- **themes**: add classic blog templates ([e30fe1d2a](https://github.com/marcantondahmen/automad/commit/e30fe1d2aff694df6970259f85c8c511b79e147e))

### Bugfixes

- **ui**: fix button block ui when nested in narrow sections ([7fa0babd6](https://github.com/marcantondahmen/automad/commit/7fa0babd67cc4432a23da7c366321ec7f13bcafb))
- **ui**: fix image select fields nested in narrow sections ([a4cd370c4](https://github.com/marcantondahmen/automad/commit/a4cd370c471fe55a026d59eecc568f0b426febd4))
- **ui**: fix inline toolbar width on small devices ([18b941734](https://github.com/marcantondahmen/automad/commit/18b941734ecb8eefdbffec9a2461bf502e4561ee))
- **ui**: fix scollbars showing up all the time on block editor toolbox ([6b9aceb2a](https://github.com/marcantondahmen/automad/commit/6b9aceb2a14c6c2bc102e5e03fc9f7128a7a5caa))
