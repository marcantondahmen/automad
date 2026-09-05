# Reporting Vulnerabilities

Security should be taken seriously whenever private data and/or the digital distribution of any data is at play. Automad is no exception here. If you believe you have discovered a security vulnerability, please read this document in its entirety before submitting a report. Security vulnerabilities must be [reported privately](https://github.com/marcantondahmen/automad/security/advisories/new). Do not open a public issue.

---

## Table of Contents

<!-- vim-markdown-toc GFM -->

- [Quality of Reports](#quality-of-reports)
- [Architecture](#architecture)
  - [Users and Roles](#users-and-roles)
  - [Sessions](#sessions)
- [Implications for Security](#implications-for-security)
  - [XSS](#xss)
  - [CSRF](#csrf)

<!-- vim-markdown-toc -->

## Quality of Reports

Unfortunately false positive vulnerability reports pose a substantial threat to cybersecurity since maintainers of open-source projects keep on drowning in reports. This implies that real threats will not get the attention that will be required to handle them properly and with care.

Before submitting a security report, you [must read](#architecture) and understand the Automad architecture described below. Reports must be based on an accurate understanding of how Automad works and must respect the security boundaries described in this policy.

A security report must:

- include a reproducible proof of concept (PoC) demonstrating the vulnerability
- demonstrate that the vulnerability crosses one of the security boundaries described below
- provide sufficient information to reproduce and verify the issue
- describe a concrete security impact

The following are not considered security vulnerabilities unless accompanied by a demonstrable violation of a relevant security boundary and concrete security impact:

- issues requiring authenticated administrator access
- issues that only affect deliberately malicious administrators
- scanner-generated findings that have not been manually validated
- CSRF claims where the affected operation is not security-sensitive
- theoretical XSS without a controllable execution path
- outdated dependencies without an exploitable path
- self-XSS (see below)

Reports that do not meet these requirements may be closed without investigation.

> [!IMPORTANT]
> Repeated submission of low-quality, speculative, or otherwise invalid security reports may result in the reporter being blocked from further reporting or participation in the Automad repository.

## Architecture

Automad is a flat-file content management system that doesn't have a database. Content is stored on disk in `.json` files. Pages are only rendered and saved as static `.html` files when content has changed. From a security perspective, this architecture has significant advantages over database driven websites.

### Users and Roles

Automad only knows two types of users &mdash; _visitors_ and _admins_. Only admins can create, delete or modify content and change settings. Visitors can only view content.

Only admins have actual user accounts on an Automad installation. They all share the same privileges. Usually there is only a single admin but it is possible to add additional ones via invitations. Visitors have no user account.

### Sessions

On every visit of an Automad site, a session is created on the server for both types of users &mdash; visitors as well as admins. On the client, a cookie is created that only contains the session id in order to identify a session. The session id and also the cookie itself don't contain any personal data or any data that can be used in order to identify an actual person.

When a visitor visits the site, also the user's session on the server doesn't contain or store any personal data. In fact it stays empty except a user chooses to persist preferences such as language or color scheme settings and as long as the installation and templates support such features.

Regarding Automad's core functionality, the session is only used to verify whether a user is signed in as an admin and therefore authorized to edit content &mdash; this is not only true for the dashboard but for the entire site in order to enable admins to edit content in the in-page editing mode.

After successfully being authenticated, the _username_ and a _csrf token_ will be stored in a user's session. During password reset requests a reset token may be stored temporarily as well. Automad itself will not store any other data than the aforementioned.

## Implications for Security

In order to fully understand possible attack vectors and the severity of reported vulnerabilities, one has to take Automad's architecture, session model and distinction between visitors and admins into account.

Admins are fully trusted users that are granted complete control over an Automad installation by design. Among other things, admins can create and modify content, change configuration, install PHP packages and templates using Composer or add JavaScript code to pages as part of normal site development and maintenance workflows.

As a consequence, reports that require authenticated admin access and only demonstrate actions that could already be performed through existing administrative capabilities are generally not considered security vulnerabilities.

To be considered a security vulnerability, _a report must demonstrate a security boundary violation_ such as privilege escalation, authentication bypass, unauthorized access, or the ability to gain capabilities beyond those already granted to an admin.

> [!IMPORTANT]
> Please make sure that you understand Automad's architecture and security model before submitting a report.

The following sections explain how these principles apply to the most commonly reported vulnerability classes in Automad.

### XSS

In general, XSS attacks imply that an _unauthorized_ user can store malicious code in some kind of data store due to the lack of sanitization of user input. This code is then typically executed in the browser by other users and can therefore be used for stealing user related data such as cookies. Typically forum software or commenting systems are exposed to such attack vectors since anybody can register and post content. In such scenarios a proper sanitization of user input is mandatory.

In Automad this kind of attacks are technically not possible due to the nature of the underlying architecture. The input of unprivileged users such as visitors is never stored or used in any way to permanently alter the system as it would be the case in a commenting system or forum.

As previously described, only admins can create, update or delete content. Please note that this also includes the ability to install templates and modify them. An admin is allowed to add executable JavaScript code to a site. It cannot be stressed enough that this ability itself doesn't pose a threat and also is fundamentally different to the nature of an XSS attack. Admins are by design privileged users that on one hand must understand their responsibility and on the other hand need the necessary freedom to actually keep a site running. This concept is not new and applies to almost every system that is connected to the internet.

Therefore the only type of user that can act as a malicious party are admins. Since visitors have no session data on the server or inside of the cookie, even a hacked admin account can't steal relevant data. This alone renders XSS attacks useless.

### CSRF

In contrast to XSS attacks, CSRF attacks potentially pose a real threat. Automad has standard measures in place in order to prevent CSRF attacks.
