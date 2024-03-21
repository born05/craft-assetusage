<?php

namespace born05\assetusage;

use Craft;
use born05\assetusage\services\Asset as AssetService;
use craft\base\Plugin as CraftPlugin;
use craft\base\Model;
use craft\console\Application as ConsoleApplication;
use craft\controllers\ElementsController;
use craft\elements\Asset;
use craft\events\DefineElementEditorHtmlEvent;
use craft\events\RegisterElementTableAttributesEvent;
use craft\events\DefineAttributeHtmlEvent;
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

        if ($this->getSettings()->renderUsedByInAssetDetail) {
            $this->registerTemplateHooks();
        }
    }

    protected function createSettingsModel(): ?Model
    {
        return new \born05\assetusage\models\Settings();
    }

    private function registerTemplateHooks()
    {
        Event::on(ElementsController::class, ElementsController::EVENT_DEFINE_EDITOR_CONTENT, function (DefineElementEditorHtmlEvent $event) {
            if ($event->element instanceof Asset) {
                /** @var Asset */
                $asset = $event->element;
                $event->html .= Craft::$app->getView()->renderTemplate('assetusage/_hooks/asset-edit-details', [
                    'elements' => $this->asset->getUsedIn($asset),
                ]);
            }
        });
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
        Event::on(Asset::class, Asset::EVENT_DEFINE_ATTRIBUTE_HTML, function (DefineAttributeHtmlEvent $event) {
            if ($event->attribute === 'usage') {
                /** @var Asset $asset */
                $asset = $event->sender;

                $event->html = $this->asset->getUsageCount($asset);

                // Prevent other event listeners from getting invoked
                $event->handled = true;
            }
        });
    }
}
