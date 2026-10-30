# Release Notes for Glue Backoffice

## 4.3.0
- Report the platform, `craft`, as `platform`, the way the Magento reporter names its own
- Report the Composer packages the project requires directly, with their installed version and description
- Fix how the report command prints versions: `'v5.6.1'` instead of `v'5.6.1'`, with no `v` added to a version that already has one or is a branch

## 4.2.0
- Report the site's PHP version

## 4.1.0
- Report the repository name, passed to the report command as `--repository-name`

## 4.0.2
- Fix Craft CMS version being shown when outputting plugin versions

## 4.0.1
- Fix env check priority

## 4.0.0
- Plugin port from 5.0.0
