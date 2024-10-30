<?php

namespace GlueAgency\Backoffice\console\controllers;

use Craft;
use craft\base\Plugin;
use craft\db\Table;
use craft\helpers\Console;
use craft\models\Site;
use GlueAgency\Backoffice\Backoffice;
use yii\console\Controller;
use yii\console\Exception;
use yii\console\ExitCode;
use yii\db\Query;

/**
 * Reports CMS data.
 */
class ReportController extends Controller
{

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
     * Reports the current Craft version and Plugin versions.
     *
     * @return int
     */
    public function actionIndex()
    {
        Backoffice::getInstance()->client->reporting()->report([
            'title'    => $name = Craft::$app->getSystemName(),
            'version' => $version = Craft::$app->getVersion(),
            'uid' => Craft::$app->getSystemUid(),
            'sites'   => $sites = array_map(function(Site $site) {
                return [
                    'title'    => $site->name,
                    'handle'   => $site->handle,
                    'language' => $site->language,
                    'enabled'  => $site->enabled,
                    'url'      => $site->baseUrl,
                    'uid'      => $site->uid,
                ];
            }, Craft::$app->getSites()->getAllSites(true)),
            'plugins' => $plugins = array_map(function(Plugin $plugin) {
                return [
                    'title'     => $plugin->name,
                    'handle'    => $plugin->handle,
                    'version'   => $plugin->version,
                    'developer' => $plugin->developer,
                    // @todo there must be a better way
                    'uid'       => (new Query)->from(Table::PLUGINS)->where(['handle' => $plugin->handle])->one()['uid'],
                ];
            }, array_values(Craft::$app->getPlugins()->getAllPlugins())),
        ]);

        $this->stdout("Reported Craft CMS '{$name}' v'{$version}'\n", Console::FG_GREEN);
        $this->stdout("Sites\n", Console::FG_GREEN);
        foreach($sites as $site) {
            $this->stdout("    Site '{$site['title']} ({$site['language']})'\n");
        }
        $this->stdout("Plugins\n", Console::FG_GREEN);
        foreach($plugins as $plugin) {
            $this->stdout("    Plugin '{$plugin['title']}' v'{$version}'\n");
        }

        return ExitCode::OK;
    }
}
