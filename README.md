# Glue Backoffice

Backoffice insight

## Requirements

This plugin requires Craft CMS 4.0.0 or later, and PHP 8.0.2 or later.

## Installation

Create a glue-backoffice.php file in the /config folder with this config

```php
return [
    'url'   => getenv('GLUE_BACKOFFICE_URL'),
    'token' => getenv('GLUE_BACKOFFICE_TOKEN'),
];
```
Finally add the env vars to your .env and .env.example.

```
# Glue Backoffice
GLUE_BACKOFFICE_URL="https://dashboard.glue.be/"
GLUE_BACKOFFICE_TOKEN=""
```

Open your terminal and run the following commands:

```bash
# Require the plugin through composer
composer require glue-agency/craft-glue-backoffice

# Install the plugin
php craft plugin/install glue-backoffice
```

## Reporting

After each deploy, report the install to the Glue Dashboard, from every environment it is deployed to.

```bash
php craft glue-backoffice/report --repository-name=git@bitbucket.org:glue-team/project.git
```

`--repository-name` is optional: without it, the repository is left out of the report.

The environment is `CRAFT_ENVIRONMENT`. `production` (and `prod` or `live`) is live: its report is the whole one, which the Dashboard keeps as the install's Craft section. Any other environment only reports where each site answers there (its uid, name, handle, language and URL) and the repository, which the Dashboard lists beside live's: no version, uid, plugins or packages. So the token goes on every host the install is deployed to, but:

- a host's `CRAFT_ENVIRONMENT` must not be `production` unless it is live: a staging host that ran as `production` would file its versions as live's;
- two hosts of one install on the same environment name overwrite each other's URLs on the Dashboard: give the second one no token;
- update the Dashboard first: one older than environments (Nov 2026) refuses a report from anywhere but live, which fails that host's deploy.

Every report says which environment it came from and prints what it sent, each site's URL included:

```
Reported Craft CMS 'Aquaduin' from the staging environment
Sites
    Site 'Aquaduin (nl)' 'https://staging-aquaduin-be.cloud.glue.be/nl'
Repository
    Name 'git@bitbucket.org:glue-team/aquaduin.git'
```

The command reports nothing, says why, and exits cleanly when `CRAFT_ENVIRONMENT` is empty (`No environment set (CRAFT_ENVIRONMENT), not reporting.`), or when a host other than live has no token (`Running in the staging environment, no Glue Dashboard token configured, not reporting.`). Live without a token fails the deploy, as before.

Live also reports the site's PHP version and the Composer packages the project requires directly: its root `composer.json`'s `require`, not its whole lock file, each with the installed version and description. Craft itself is among them as `craftcms/cms`, and so are the plugins; platform requirements (`php`, `ext-*`) are left out.

From a Deployer `deploy.php`:

```php
desc('Report to backoffice');
task('glue:backoffice_report', function() {
    call_user_func(craft('glue-backoffice/report --repository-name={{repository}}'));
});

after('deploy:symlink', 'glue:backoffice_report');
```
