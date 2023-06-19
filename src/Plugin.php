<?php

namespace born05\assetusage;

use Craft;
use born05\assetusage\services\Asset as AssetService;
use craft\base\Plugin as CraftPlugin;
use craft\base\Model;
use craft\console\Application as ConsoleApplication;
use craft\elements\Asset;
use craft\events\RegisterElementTableAttributesEvent;
use craft\events\SetElementTableAttributeHtmlEvent;
use yii\base\Event;

class Plugin extends CraftPlugin
{
    public string $schemaVersion = '2.0.0';

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Plugin::$plugin
     */
    public static Plugin $plugin;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        if (!$this->isInstalled) {
            return;
        }

        // Register Components (Services)
        $this->setComponents([
            'asset' => AssetService::class,
        ]);

        // Add in our console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'born05\assetusage\console\controllers';
        }

        $this->registerTableAttributes();
    }

    protected function createSettingsModel(): ?Model
    {
        return new \born05\assetusage\models\Settings();
    }

    /**
     * Adds the following attributes to the asset fields in CMS
     * NOTE: You still need to select them with the 'gear'
     */
    private function registerTableAttributes()
    {
        Event::on(Asset::class, Asset::EVENT_REGISTER_TABLE_ATTRIBUTES, function (RegisterElementTableAttributesEvent $event) {
            $event->tableAttributes['usage'] = [
                'label' => Craft::t('assetusage', 'Usage'),
            ];
        });

        Event::on(Asset::class, Asset::EVENT_SET_TABLE_ATTRIBUTE_HTML, function (SetElementTableAttributeHtmlEvent $event) {
            if ($event->attribute === 'usage') {
                /** @var Asset $asset */
                $asset = $event->sender;
                $event->html = $this->asset->getUsage($asset);

                // Prevent other event listeners from getting invoked
                $event->handled = true;
            }
        });
    }
}
