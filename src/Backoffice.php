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

    public $schemaVersion = '1.0.0';

    /**
     * @return void
     */
    public function init()
    {
        parent::init();

        Craft::setAlias('@glue-backoffice', __DIR__);

        $this->setComponents([
            'client' => GlueClient::class,
        ]);

        $this->registerControllers();
    }

    /**
     * @throws \yii\base\InvalidConfigException
     *
     * @return Model|object|null
     */
    protected function createSettingsModel()
    {
        return Craft::createObject(Settings::class);
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \yii\base\Exception
     *
     * @return string|null
     */
    protected function settingsHtml()
    {
        return Craft::$app->view->renderTemplate('glue-backoffice/settings/index.twig', [
            'plugin'   => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    /**
     * @return void
     */
    protected function registerControllers()
    {
        if(Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'GlueAgency\\Backoffice\\console\\controllers';

            return;
        }

        $this->controllerNamespace = 'GlueAgency\\Backoffice\\controllers';
    }

    /**
     * @return void
     */
    protected function registerCpPluginTemplates()
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
