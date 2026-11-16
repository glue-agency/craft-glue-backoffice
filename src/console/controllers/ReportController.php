<?php

namespace GlueAgency\Backoffice\console\controllers;

use Craft;
use craft\helpers\App;
use craft\helpers\Console;
use craft\models\Site;
use GlueAgency\Backoffice\Backoffice;
use GlueAgency\Backoffice\dto\Package;
use GlueAgency\Backoffice\dto\Plugin;
use yii\console\Controller;
use yii\console\Exception;
use yii\console\ExitCode;

/**
 * Reports the install to the Glue Dashboard from every environment it is
 * deployed to: live sends the whole report, any other only where each site
 * answers there, which is all the Dashboard keeps of one.
 */
class ReportController extends Controller
{

    /**
     * The names the Dashboard reads as live.
     */
    protected const LIVE = ['production', 'prod', 'live'];

    /**
     * @var ?string The Bitbucket repository this install deploys from.
     */
    public ?string $repositoryName = null;

    public function options($actionID): array
    {
        return array_merge(parent::options($actionID), [
            'repositoryName',
        ]);
    }

    public function beforeAction($action): bool
    {
        $environment = $this->environment();

        if($environment === '') {
            $this->stdout("No environment set (CRAFT_ENVIRONMENT), not reporting.\n");

            return false;
        }

        $settings = Backoffice::getInstance()->settings;

        if(! $settings->url) {
            throw new Exception('The master url is missing.');
        }

        if(! $settings->token) {
            if(static::isLive($environment)) {
                throw new Exception('The Authorization token is missing.');
            }

            $this->stdout("Running in the {$environment} environment, no Glue Dashboard token configured, not reporting.\n");

            return false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Reports the current Craft version, Plugin versions and Composer packages
     * from live, and from any other environment where each site answers.
     *
     * @return int
     */
    public function actionIndex()
    {
        $environment = $this->environment();
        $sites = Craft::$app->getSites()->getAllSites(true);

        if(! static::isLive($environment)) {
            return $this->reportEnvironment($environment, $sites);
        }

        Backoffice::getInstance()->client->reporting()->report([
            'platform'    => 'craft',
            'environment' => $environment,
            'title'       => $name = Craft::$app->getSystemName(),
            'version'     => $version = Craft::$app->getVersion(),
            'phpVersion'  => $phpVersion = App::phpVersion(),
            'uid'         => Craft::$app->getSystemUid(),
            'sites'       => array_map(function(Site $site) {
                return [
                    'title'    => $site->name,
                    'handle'   => $site->handle,
                    'language' => $site->language,
                    'enabled'  => $site->enabled,
                    'url'      => $site->baseUrl,
                    'uid'      => $site->uid,
                ];
            }, $sites),
            'plugins'     => array_map(function(Plugin $plugin) {
                return [
                    'title'     => $plugin->name,
                    'handle'    => $plugin->handle,
                    'version'   => $plugin->version,
                    'developer' => $plugin->developer,
                    'uid'       => $plugin->uid,
                ];
            }, $plugins = Backoffice::getInstance()->plugins->getAllPlugins()),
            'packages'    => array_map(function(Package $package) {
                return [
                    'name'        => $package->name,
                    'version'     => $package->version,
                    'description' => $package->getTruncatedDescription(),
                ];
            }, $packages = Backoffice::getInstance()->packages->getAllPackages()),
            'repository'  => [
                'name' => $this->repositoryName,
            ],
        ]);

        $this->stdout("Reported Craft CMS '{$name}' 'v{$version}' from the {$environment} environment\n", Console::FG_GREEN);
        $this->stdout("    PHP 'v{$phpVersion}'\n");
        $this->printSites($sites);
        $this->stdout("Plugins\n", Console::FG_GREEN);
        foreach($plugins as $plugin) {
            $this->stdout("    Plugin '{$plugin->name}' '{$plugin->getFormattedVersion()}'\n");
        }
        $this->stdout("Packages\n", Console::FG_GREEN);
        foreach($packages as $package) {
            $this->stdout("    Package '{$package->name}' '{$package->getFormattedVersion()}'\n");
        }
        $this->stdout("Repository\n", Console::FG_GREEN);
        $this->stdout("    Name '{$this->repositoryName}'\n");

        return ExitCode::OK;
    }

    /**
     * Only where each site answers: no version or uid, so a Dashboard too old
     * to know environments refuses the report rather than file it as live.
     *
     * @param Site[] $sites
     */
    protected function reportEnvironment(string $environment, array $sites): int
    {
        Backoffice::getInstance()->client->reporting()->report([
            'platform'    => 'craft',
            'environment' => $environment,
            'title'       => $name = Craft::$app->getSystemName(),
            'sites'       => array_map(function(Site $site) {
                return [
                    'title'    => $site->name,
                    'handle'   => $site->handle,
                    'language' => $site->language,
                    'url'      => $site->baseUrl,
                    'uid'      => $site->uid,
                ];
            }, $sites),
            'repository'  => [
                'name' => $this->repositoryName,
            ],
        ]);

        $this->stdout("Reported Craft CMS '{$name}' from the {$environment} environment\n", Console::FG_GREEN);
        $this->printSites($sites);
        $this->stdout("Repository\n", Console::FG_GREEN);
        $this->stdout("    Name '{$this->repositoryName}'\n");

        return ExitCode::OK;
    }

    /**
     * @param Site[] $sites
     */
    protected function printSites(array $sites): void
    {
        $this->stdout("Sites\n", Console::FG_GREEN);
        foreach($sites as $site) {
            $this->stdout("    Site '{$site->name} ({$site->language})' '{$site->baseUrl}'\n");
        }
    }

    /**
     * The environment this host runs in, as CRAFT_ENVIRONMENT names it.
     */
    protected function environment(): string
    {
        return trim((string) Craft::$app->env);
    }

    protected static function isLive(string $environment): bool
    {
        return in_array(strtolower($environment), static::LIVE, true);
    }
}
