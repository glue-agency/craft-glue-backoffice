<?php

namespace GlueAgency\Backoffice\services;

use Craft;
use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use GlueAgency\Backoffice\dto\Package;
use yii\base\Component;

/**
 * The Composer packages the project requires directly, at the version installed.
 */
class Packages extends Component
{

    /**
     * @return Package[]
     */
    public function getAllPackages(): array
    {
        $installed = $this->installedPackages();
        $packages = [];

        foreach(array_keys(Craft::$app->getComposer()->getConfig()['require'] ?? []) as $name) {
            // Platform requirements (php, ext-*) are never installed, so this drops them too.
            if(! isset($installed[$name])) {
                continue;
            }

            $packages[] = new Package($name, $installed[$name]['version'], $installed[$name]['description'] ?? '');
        }

        return $packages;
    }

    protected function installedPackages(): array
    {
        $installed = Json::decodeFromFile(Craft::$app->getVendorPath() . '/composer/installed.json');

        return ArrayHelper::index($installed['packages'] ?? [], 'name');
    }
}
