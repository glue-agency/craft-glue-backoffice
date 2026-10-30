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
 * Reports CMS data.
 */
class ReportController extends Controller
{

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
        if(Craft::$app->env !== 'production') {
            $this->stdout('Not running in production mode. Not reporting.', Console::BG_RED);

            return ExitCode::OK;
        }

        $settings = Backoffice::getInstance()->settings;

        if(! $settings->url) {
            throw new Exception('The master url is missing.');
        }

        if(! $settings->token) {
            throw new Exception('The Authorization token is missing.');
        }

        return parent::beforeAction($action);
    }

    /**
     * Reports the current Craft version, Plugin versions and Composer packages.
     *
     * @return int
     */
    public function actionIndex()
    {
        Backoffice::getInstance()->client->reporting()->report([
            'title'      => $name = Craft::$app->getSystemName(),
            'version'    => $version = Craft::$app->getVersion(),
            'phpVersion' => $phpVersion = App::phpVersion(),
            'uid'        => Craft::$app->getSystemUid(),
            'sites'      => $sites = array_map(function(Site $site) {
                return [
                    'title'    => $site->name,
                    'handle'   => $site->handle,
                    'language' => $site->language,
                    'enabled'  => $site->enabled,
                    'url'      => $site->baseUrl,
                    'uid'      => $site->uid,
                ];
            }, Craft::$app->getSites()->getAllSites(true)),
            'plugins'    => $plugins = array_map(function(Plugin $plugin) {
                return [
                    'title'     => $plugin->name,
                    'handle'    => $plugin->handle,
                    'version'   => $plugin->version,
                    'developer' => $plugin->developer,
                    'uid'       => $plugin->uid,
                ];
            }, Backoffice::getInstance()->plugins->getAllPlugins()),
            'packages'   => $packages = array_map(function(Package $package) {
                return [
                    'name'        => $package->name,
                    'version'     => $package->version,
                    'description' => $package->getTruncatedDescription(),
                ];
            }, Backoffice::getInstance()->packages->getAllPackages()),
            'repository' => [
                'name' => $this->repositoryName,
            ],
        ]);

        $this->stdout("Reported Craft CMS '{$name}' v'{$version}'\n", Console::FG_GREEN);
        $this->stdout("    PHP v'{$phpVersion}'\n");
        $this->stdout("Sites\n", Console::FG_GREEN);
        foreach($sites as $site) {
            $this->stdout("    Site '{$site['title']} ({$site['language']})'\n");
        }
        $this->stdout("Plugins\n", Console::FG_GREEN);
        foreach($plugins as $plugin) {
            $this->stdout("    Plugin '{$plugin['title']}' v'{$plugin['version']}'\n");
        }
        $this->stdout("Packages\n", Console::FG_GREEN);
        foreach($packages as $package) {
            $this->stdout("    Package '{$package['name']}' v'{$package['version']}'\n");
        }
        $this->stdout("Repository\n", Console::FG_GREEN);
        $this->stdout("    Name '{$this->repositoryName}'\n");

        return ExitCode::OK;
    }
}
