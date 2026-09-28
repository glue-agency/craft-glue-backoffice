# Glue Backoffice

Backoffice and insight

## Requirements

This plugin requires Craft CMS 5.0.0 or later, and PHP 8.2 or later.

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

After each deploy, report the install to the Glue Dashboard. The command only reports in the `production` environment.

```bash
php craft glue-backoffice/report --repository-name=git@bitbucket.org:glue-team/project.git
```

`--repository-name` is optional: without it, the repository is left out of the report.

From a Deployer `deploy.php`:

```php
desc('Report to backoffice');
task('glue:backoffice_report', function() {
    call_user_func(craft('glue-backoffice/report --repository-name={{repository}}'));
});

after('deploy:symlink', 'glue:backoffice_report');
```
