# Changelog

## [v2.0.0-alpha.7](https://github.com/marcantondahmen/automad/commit/5ecf43618492a8260e3f5e06dbbe513537a912cc)

Sun, 13 Oct 2024 20:51:29 +0200

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

## [v1.9.1](https://github.com/marcantondahmen/automad/commit/6d5c90724027d24010b4501daa411f856f673b25)

Thu, 14 Oct 2021 13:11:38 +0200

### New Features

- **engine**: allow overriding of snippets after including a template ([f2ed64e6f](https://github.com/marcantondahmen/automad/commit/f2ed64e6f8ab4116c629d476067155e5f9b4c62d))

### Bugfixes

- **theme**: fix position of prev-next navigation ([83e9bf41f](https://github.com/marcantondahmen/automad/commit/83e9bf41fc26eb9e94df280f679fbafd23780d93))

## [v1.9.0](https://github.com/marcantondahmen/automad/commit/dff5972edf04c435b853e573259021a4f6fc198d)

Wed, 13 Oct 2021 23:15:28 +0200

### Breaking Changes

- set required PHP version to 7.4+ ([0ed244f30](https://github.com/marcantondahmen/automad/commit/0ed244f308340f685b17c4cfe305dae802d298e7))

### New Features

- **blocks**: add support for nested lists ([1c8966be6](https://github.com/marcantondahmen/automad/commit/1c8966be6b46713977c0f14b4ee164a641baa530))
- **blocks**: upgrade table block ([0d8d8d0e0](https://github.com/marcantondahmen/automad/commit/0d8d8d0e0307f1bb16e65e374f1dd18c6f3bb077))
- **engine**: add cache busting timestamps to assets ([8a06b4374](https://github.com/marcantondahmen/automad/commit/8a06b4374bab5cc016b7e7e0bd12211fc37f6ed0))
- **engine**: improve template inheritance ([2a6baaa28](https://github.com/marcantondahmen/automad/commit/2a6baaa28ae297feddfa32fa6fb4c67a1b05be7b))
- **engine**: snippets can't be redefined in order to enable a more flexible inheritance of templates ([8f0e7564b](https://github.com/marcantondahmen/automad/commit/8f0e7564b0afe4a4b0a51b628d08edcf69421e90))
- **ui**: add option to change username and add email ([62a4e219b](https://github.com/marcantondahmen/automad/commit/62a4e219bb677ea37ac164a25c6207798ee34ee8))
- **ui**: add option to invite users by email ([b281a8330](https://github.com/marcantondahmen/automad/commit/b281a8330a05295f74aec4679e94f03867e035fc))
- **ui**: add option to reset passwords by email ([2abc11d10](https://github.com/marcantondahmen/automad/commit/2abc11d101d21bcd4eb9cb1b738f7c193d24de3f))
- set required PHP version to 7.4+ ([0ed244f30](https://github.com/marcantondahmen/automad/commit/0ed244f308340f685b17c4cfe305dae802d298e7))

### Bugfixes

- **ui**: fix empty button for registered users before the status has been updated ([268cc868d](https://github.com/marcantondahmen/automad/commit/268cc868d50b86b995bee2596999c1041dd3aa52))
- **ui**: fix text modules for search and replace ([3e698295b](https://github.com/marcantondahmen/automad/commit/3e698295bda14a724aae109e3ec060c92e9d953c))
- fix error when moving cache files to tmp on other drive ([8003aa6d4](https://github.com/marcantondahmen/automad/commit/8003aa6d48a30e550bced421a4d6bac39668aa07))

## [v1.8.7](https://github.com/marcantondahmen/automad/commit/8bbc8f2da0b868efd22ae9459b1bbc539d65ba49)

Thu, 9 Sep 2021 01:02:47 +0200

### Breaking Changes

- set required PHP version to 7.2+ ([8abbf5107](https://github.com/marcantondahmen/automad/commit/8abbf5107b1865a649a3bc05bf67006f3467e746))
- **core**: refactor Parse class ([39a8c4f47](https://github.com/marcantondahmen/automad/commit/39a8c4f47c849392fcb438917d2c0a8ec7725eed))

### New Features

- set required PHP version to 7.2+ ([8abbf5107](https://github.com/marcantondahmen/automad/commit/8abbf5107b1865a649a3bc05bf67006f3467e746))

### Bugfixes

- **ui**: debounce status requests ([b3b54130b](https://github.com/marcantondahmen/automad/commit/b3b54130bc1b603a1834851876a2a6c396288523))
- **ui**: fix switcher rendering in Safari ([2019c80ea](https://github.com/marcantondahmen/automad/commit/2019c80ea91fac2a2177917f02db8124694ab838))

## [v1.8.6](https://github.com/marcantondahmen/automad/commit/cff6cb56fed110a8c3e9889c5c6eaa3ae0ca89e1)

Tue, 31 Aug 2021 21:01:29 +0200

### Bugfixes

- **system**: update Composer to version 2.1.6 in order to fix issues on Windows ([81cc05972](https://github.com/marcantondahmen/automad/commit/81cc05972ae4d10202adfe062ecc7b70f540edff))
- **ui**: fix block editor toolbar on small devices ([6c81c4378](https://github.com/marcantondahmen/automad/commit/6c81c43780be143a01018d388d192d06690c61bd))

## [v1.8.5](https://github.com/marcantondahmen/automad/commit/09942a0ab9c92aaacf651e7b7d359e0cc5f47459)

Fri, 13 Aug 2021 01:21:26 +0200

### Bugfixes

- **ui**: remove slug input from homepage settings ([38a0ca8e5](https://github.com/marcantondahmen/automad/commit/38a0ca8e5d37fd00c3acc86d5f9d10062cf2d9e8))

## [v1.8.4](https://github.com/marcantondahmen/automad/commit/5512e55549473e27a2223803caa52200cdc70800)

Thu, 12 Aug 2021 22:24:51 +0200

### New Features

- **core**: add Str::slug() method to sanitize ids and directory names ([4ee87a3bc](https://github.com/marcantondahmen/automad/commit/4ee87a3bc521921af36c19414a7e23eb730f9595))
- **ui**: add chinese, japanese and korean translations ([a287a740e](https://github.com/marcantondahmen/automad/commit/a287a740ed4028278f799112c3723fbfb733efb2))
- **ui**: add option to define a custom slug for a page ([c593fa29d](https://github.com/marcantondahmen/automad/commit/c593fa29d73edb5c7d0ca9c9649d3b5b9c52863e))

### Bugfixes

- **ui**: fix typo in text modules ([ba8702a87](https://github.com/marcantondahmen/automad/commit/ba8702a87d26afd0172294d541c1573925f77f56))
- **ui**: fix updating links that are wrapped in quotes ([4417468d6](https://github.com/marcantondahmen/automad/commit/4417468d659c1e71e9b76698e377a19a1e209b5a))
