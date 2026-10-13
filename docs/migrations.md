# Migrations

Data that was saved by older versions of Automad sometimes has to be converted to a new format. Such conversions are called migrations. They run automatically as part of the setup.

## The Setup

The setup is a one-time process that runs after a fresh installation and after every update. It is started automatically on the next visit of the dashboard and consists of two parts:

1. **Migrations** — all migrations are run in a fixed order.
2. **Wizard** — the setup wizard asks for missing settings, for example the mail configuration.

Automad keeps track of a completed setup using the marker file `automad/.setup-completed`. Since every update replaces the `automad` directory, the marker file is removed and the setup runs again after each update.

## Available Migrations

| Migration            | Description                                                                                                              |
| -------------------- | ------------------------------------------------------------------------------------------------------------------------ |
| Config PHP strings   | Converts legacy files in `config` that only contain a plain string into PHP files that don't expose their content.        |
| Encrypt config files | Encrypts files in `config` that contain secrets in case an encryption key is provided. See [Encrypting Secrets](encrypting-secrets.md). |

Every migration only changes data that was not migrated yet. It is therefore safe to run all migrations multiple times. A failing migration is logged in the debug log and doesn't stop the remaining migrations.

## Running Migrations Manually

Migrations can also be run at any time using the console, for example after adding an encryption key:

```bash
php automad/console migrate
```

The command prints the result of every migration and exits with an error code in case one of them failed. Make sure to run the command as the same user as the web server in order to not mess up file permissions. Note that environment variables that are defined in a `.htaccess` file or in the web server configuration are not available on the command line and have to be exported in the shell.

Alternatively, deleting the marker file `automad/.setup-completed` restarts the full setup including the wizard on the next visit of the dashboard.

> [!NOTE]
> The `migrate` command used to import content from Automad version 1 in earlier releases. That command is now called `import:v1`.

## For Developers

- Migrations are classes extending `Automad\System\Setup\Migrations\AbstractMigration` in `automad/src/server/System/Setup/Migrations/` and implement `getName()` and `run()`.
- New migrations must be **appended** to the list in the constructor of `Automad\System\Setup\Migration`. There is no automatic discovery, since the order matters.
- Migrations must be idempotent. All migrations run again after every update and when running the console command, so running a migration on already migrated data must not change anything.
- Never migrate data lazily when reading it. Config files are already read during `Config::init()`, before any permission constants are defined.
- Never run migrations in `System\Update::run()`, since that code is still executed by the old version during an update.
- `Migration::run()` returns the results of all migrations by name — `null` on success or the error message in case of a failure. The constructor accepts a custom list of migrations for testing. Tests live in `automad/tests/main/src/System/Setup/`.
