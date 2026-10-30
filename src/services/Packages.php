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
    public function getAllPackages()
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

    /**
     * @return array
     */
    protected function installedPackages()
    {
        $installed = Json::decode(file_get_contents(Craft::$app->getVendorPath() . '/composer/installed.json'));

        // Composer 1 writes a bare list, Composer 2 nests it under packages.
        return ArrayHelper::index($installed['packages'] ?? $installed, 'name');
    }
}
