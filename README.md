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
