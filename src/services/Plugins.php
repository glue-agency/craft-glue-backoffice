<?php

namespace GlueAgency\Backoffice\services;

use Craft;
use craft\base\Plugin as CraftPlugin;
use craft\db\Query;
use craft\db\Table;
use GlueAgency\Backoffice\dto\Plugin;
use yii\base\Component;

/**
 * The installed Craft plugins, with the uid Craft stores each one under.
 */
class Plugins extends Component
{

    /**
     * @return Plugin[]
     */
    public function getAllPlugins()
    {
        $uids = (new Query)
            ->select(['handle', 'uid'])
            ->from(Table::PLUGINS)
            ->pairs();

        return array_map(function(CraftPlugin $plugin) use ($uids) {
            return new Plugin(
                $plugin->name,
                $plugin->handle,
                $plugin->version,
                $plugin->developer,
                $uids[$plugin->handle] ?? null
            );
        }, array_values(Craft::$app->getPlugins()->getAllPlugins()));
    }
}
