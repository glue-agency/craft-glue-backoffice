# Release Notes for Glue Backoffice

## 5.4.0
- Report from every environment, named by `CRAFT_ENVIRONMENT`: `production` (or `prod`, `live`) sends the whole report, any other environment only where each site answers there, which the Dashboard lists beside live's
- Send the environment with live's report too
- Without a token, a host other than live names its environment and reports nothing; live still fails the deploy
- Every report says which environment it came from and prints each site's URL
- Needs a Dashboard that knows environments (Nov 2026): an older one refuses a report from anywhere but live

## 5.3.0
- Report the platform, `craft`, as `platform`, the way the Magento reporter names its own
- Report the Composer packages the project requires directly, with their installed version and description
- Fix how the report command prints versions: `'v5.6.1'` instead of `v'5.6.1'`, with no `v` added to a version that already has one or is a branch

## 5.2.0
- Report the site's PHP version

## 5.1.0
- Report the repository name, passed to the report command as `--repository-name`

## 5.0.2
- Fix Craft CMS version being shown when outputting plugin versions

## 5.0.1
- Fix env check priority

## 5.0.0
- Initial Plugin release
