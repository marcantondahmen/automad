# Reporting Vulnerabilities

Security should be taken seriously whenever private data and/or the digital distribution of any data is at play. Automad is **no exception** here.

Whenever you encounter a security vulnerability, please feel free to [report it privately](https://github.com/marcantondahmen/automad/security) and provide the following information:

- A brief description of the vulnerability
- A use case for an exploit and a valid attack vector
- All required steps in order to enable an attacker to exploit the vulnerability

> [!IMPORTANT]
> Please note that pull-requests for this repository will be ignored as stated in the README.

## Quality of Reports

Unfortunately false positive vulnerability reports pose a substantial threat to cybersecurity since maintainers of open-source projects keep on drowning in reports. This implies that real threats will not get the attention that will be required to handle them properly and with care.

However, _all reported vulnerabilities are reviewed_ and handled with priority as soon as possible.

Please note that after an initial triage only reports of exploitable vulnerabilities with realistic attack vectors are followed up. Please make sure that you are famliliar with Automad's architecture and fully understand its implications for security as described below.

## Architecture

Automad is a _flat-file_ content management system that doesn't have a database. Content is stored on disk in `.json` files. Pages are only rendered and saved as static `.html` files when content has changed. From a security perspective, this architecture has significant advantages over database driven websites.

### Users and Roles

Automad only knows two types of users &mdash; _visitors_ and _admins_. Only admins can create, delete or modify content and change settings. Visitors can only view content.

Only admins have actual user accounts on an Automad installation. They all share the same privileges. Usually there is only a single admin but it is possible to add additional ones via invitations. Visitors have no user account.

### Sessions

On every visit of an Automad site, a session is created on the server for both types of users &mdash; visitors as well as admins. On the client, a cookie is created that only contains the session id in order to identify a session. The session id and also the cookie itself don't contain any personal data or any data that can be used in order to identify an actual person.

When a visitor visits the site, also the user's session on the server doesn't contain or store any personal data. In fact it stays empty except a user chooses to persist preferences such as language or color scheme settings and as long as the installation and templates support such features.

Regarding Automad's core functionality, the session is only used to verify whether a user is signed in as an admin and therefore authorized to edit content &mdash; this is not only true for the dashboard but for the entire site in order to enable admins to edit content in the in-page editing mode.

After successfully being authenticated, the _username_ and a _csrf token_ will be stored in a user's session. During password reset requests a reset token may be stored temporarily as well. Automad itself will not store any other data than the aforementioned.

### Implications for Security

In order to fully understand possible attack vectors and the severity of reported vulnerabilities, one has to take the architectural concept, the way sessions work in Automad and the limitation of the visitor role into account. Generally, vulnerabilities can be broken down into two categories &mdash; _XSS (cross-site-scripting)_ and _CSRF (cross-site-request-forgery)_.

#### XSS

In general, XSS attacks imply that an **unauthorized** user can store malicious code in some kind of data store due to the lack of sanitization of user input. This code is then typically executed in the browser by other users and can therefore be used for stealing user related data such as cookies. Typically forum software or commenting systems are exposed to such attack vectors since anybody can register and post content. In such scenarios a proper sanitization of user input is mandatory.

In Automad this kind of attacks are technically not possible due to the nature of the underlying architecture. The input of unprivileged users such as visitors is never stored or used in any way to permanently alter the system as it would be the case in a commenting system or forum.

As previously described, only admins can create, update or delete content. Please note that this also includes the ability to install templates and modify them. An admin is allowed to add executable JavaScript code to a site. It cannot be stressed enough that this ability itself doesn't pose a threat and also is fundamentally different to the nature of an XSS attack. Admins are by design privileged users that on one hand must understand their responsibility and on the other hand need the necessary freedom to actually keep a site running. This concept is not new and applies to almost every system that is connected to the internet.

Therefore the only type of user that can act as a malicious party are admins. Since visitors have no session data on the server or inside of the cookie, even a hacked admin account can't steal relevant data. This alone renders XSS attacks useless.

#### CSRF

In contrast to XSS attacks, CSRF attacks potentially pose a real threat. Automad has standard measures in place in order to prevent CSRF attacks.
