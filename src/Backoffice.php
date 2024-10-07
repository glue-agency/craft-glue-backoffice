<?php

namespace GlueAgency\Backoffice;

use Craft;
use GlueAgency\Backoffice\clients\GlueClient;
use GlueAgency\Backoffice\models\Settings;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterTemplateRootsEvent;
use craft\web\View;
use yii\base\Event;

/**
 * Glue Backoffice plugin
 *
 * @property-read GlueClient     $client
 *
 * @method static Backoffice getInstance()
 *
 * @method Settings getSettings()
 * @method GlueClient getClient()
 *
 * @author    Glue Agency <support@glue.be>
 * @copyright Glue Agency
 * @license   MIT
 */
class Backoffice extends Plugin
{

    public string $schemaVersion = '1.0.0';

    public static function config(): array
    {
        return [
            'components' => [
                'client' => GlueClient::class,
            ],
        ];
    }

    public function init(): void
    {
        parent::init();

        Craft::setAlias('@glue-backoffice', __DIR__);

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function() {
            $this->registerControllers();
        });
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('glue-backoffice/settings/index.twig', [
            'plugin'   => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    protected function registerControllers(): void
    {
        if(Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'GlueAgency\\Backoffice\\console\\controllers';

            return;
        }

        $this->controllerNamespace = 'GlueAgency\\Backoffice\\controllers';
    }

    protected function registerCpPluginTemplates(): void
    {
        Event::on(
            View::class,
            View::EVENT_REGISTER_CP_TEMPLATE_ROOTS,
            function(RegisterTemplateRootsEvent $event) {
                $event->roots['glue-backoffice'] = '@glue-backoffice/templates';
            }
        );
    }
}
