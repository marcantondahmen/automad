# Encrypting Secrets

Automad stores credentials, such as the mail server password or AI provider API keys, as PHP files in the `config` directory. Those files are executed instead of being displayed when they are requested directly, and they are additionally protected by server rules. As a second layer of protection, Automad can encrypt the stored secrets. In case such a file ever leaks — for example because PHP is temporarily not executed by a misconfigured server, a backup ends up in the wrong place or the directory is accidentally committed to a repository — the secrets are not readable without the encryption key.

Encryption is optional and disabled by default. It is enabled automatically as soon as an encryption key is provided.

## What is Encrypted

| File                       | Content                          | Encrypted |
| -------------------------- | -------------------------------- | --------- |
| `config/config.mail.php`   | Mail server settings             | Yes       |
| `config/ai.*.php`          | AI provider API keys             | Yes       |
| `config/composer.auth.php` | Composer authentication          | Yes       |
| `config/tokens.auth.php`   | Hashes of MCP access tokens      | Yes       |
| `config/config.php`        | Main configuration               | No        |
| `config/accounts.php`      | User accounts and password hashes | No        |

The main configuration stays unencrypted in order to be editable manually. User accounts are not encrypted on purpose, since a lost key would otherwise lock out all users. They only contain password hashes anyway.

## Creating a Key

A key must be at least 32 characters long. Shorter keys are ignored and encryption stays disabled. A good key can be generated as follows:

```bash
openssl rand -hex 32
```

A hex key only contains letters and digits and can therefore be used in all kinds of configuration files, such as `.htaccess`, server configurations or shell scripts, without any quoting.

## Providing the Key

There are two environment variables that can be used to provide the key:

- `AM_ENCRYPTION_KEY_FILE` — the path to a plain text file that only contains the key (recommended)
- `AM_ENCRYPTION_KEY` — the key itself

In case both are set, `AM_ENCRYPTION_KEY` takes precedence.

Using a key file is recommended, since only the path to the key is part of the server configuration. That way the key is not exposed in case the server configuration, for example a `.htaccess` file, leaks or is committed to a repository. The key file should be stored outside of the document root whenever possible and must be readable by the web server. Relative paths are resolved against the Automad base directory — the directory that contains the `index.php` file. A path like `../automad.key` therefore points to a file next to the document root and keeps working when the site is moved together with that file.

> [!NOTE]
> Some shared hosting providers restrict PHP from reading files outside of the document root by using `open_basedir`. In case the key file can't be read, encryption stays disabled.

### Apache and LiteSpeed

The `.htaccess` file that ships with Automad already contains a commented example. Uncomment the variable you want to use:

```apache
<IfModule mod_env.c>
	SetEnv AM_ENCRYPTION_KEY_FILE "../automad.key"
</IfModule>
```

### Nginx

Add a `fastcgi_param` to the PHP location block:

```nginx
fastcgi_param AM_ENCRYPTION_KEY_FILE /var/www/automad.key;
```

### Caddy and FrankenPHP

Use the `env` subdirective of `php_fastcgi` (Caddy) or `php_server` (FrankenPHP):

```caddyfile
php_fastcgi php:9000 {
	env AM_ENCRYPTION_KEY_FILE /var/www/automad.key
}
```

### Docker and Hosting Panels

Environment variables that are defined in a container or in a hosting control panel work as well, as long as they are passed to PHP. Note that PHP-FPM drops environment variables by default (`clear_env = yes`). In that case either set `clear_env = no` or forward the variable explicitly in the pool configuration, for example `env[AM_ENCRYPTION_KEY_FILE] = $AM_ENCRYPTION_KEY_FILE`. Variables passed by the web server as shown above are not affected by `clear_env`.

### Local Development

During development, the key can also be added to the `.env` file in the base directory. Note that `.env` files are only meant for development and must never be deployed to a production server.

## Encrypting Existing Files

Once a key is provided, files are encrypted the next time they are saved, for example when changing the mail settings in the dashboard. All files are also encrypted automatically after the next update. In order to encrypt all files right away, run the migrations manually using the console:

```bash
AM_ENCRYPTION_KEY_FILE=../automad.key php automad/console migrate
```

> [!IMPORTANT]
> Variables that are defined in a `.htaccess` file or in the web server configuration are not available on the command line. They have to be exported in the shell as shown above. Also make sure to run the command as the same user as the web server in order to not mess up file permissions.

See also [Migrations](migrations.md).

## Changing or Losing the Key

Encrypted secrets can only be decrypted with the key that was used to encrypt them. When the key is changed or removed, all encrypted settings are treated as not configured. In that case the setup wizard asks for the mail settings again on the next login and AI provider keys, Composer authentication and MCP access tokens have to be added again in the dashboard. There is no other way of rotating a key.

Keep this in mind when moving a site to another server and make sure to also move the key file or variable.

## What Encryption Doesn't Protect Against

Encryption protects secrets in leaked files. It doesn't protect against an attacker that is able to execute code on the server or read the server's environment, since Automad itself must be able to read the key.

## For Developers

- Secrets are written with `FileSystem::writePhpString($file, $data, true)` and read with `FileSystem::readPhpString()`, which decrypts transparently and returns `null` in case decryption fails. Readers must treat `null` as "not configured". Without a key, the `true` flag has no effect and the data is stored as plain text.
- Objects are stored using `Automad\System\SerializedFile` with the `$encrypt` constructor argument set to `true`. It takes care of encryption, rename-safe serialization and restricting unserialization to the expected class.
- Encryption is implemented in `Automad\System\Encryption` using AES-256-GCM (`openssl_encrypt`). Encrypted strings are prefixed with `amenc:v1:`, which makes detecting encrypted content a simple string comparison. The actual key is derived from the provided key using HKDF.
- The key is read directly from `getenv()` and `$_SERVER` (including the `REDIRECT_` prefix that Apache adds after rewriting) and must never be defined as a constant.
- Reading the key and decrypting must never write or log anything, since config files are already read during `Config::init()`.
- When adding a new file that contains secrets, also add its name to `EncryptConfigFiles::PATTERNS` in `automad/src/server/System/Setup/Migrations/EncryptConfigFiles.php`, so that existing installations encrypt it on the next update. Never add `config.php` or `accounts.php`.
