<?php

namespace born05\assetusage;

use Craft;
use born05\assetusage\services\Asset as AssetService;
use craft\base\Plugin as CraftPlugin;
use craft\elements\Asset;
use craft\events\RegisterElementTableAttributesEvent;
use craft\events\SetElementTableAttributeHtmlEvent;
use yii\base\Event;

class Plugin extends CraftPlugin
{
    /**
     * @var string
     */
    public $schemaVersion = '2.0.0';

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Plugin::$plugin
     *
     * @var Plugin
     */
    public static $plugin;

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        if (!$this->isInstalled) return;

        // Register Components (Services)
        $this->setComponents([
            'asset' => AssetService::class,
        ]);

        /**
         * Adds the following attributes to the asset fields in CMS
         * NOTE: You still need to select them with the 'gear'
         *
         * @return array
         */
        Event::on(Asset::class, Asset::EVENT_REGISTER_TABLE_ATTRIBUTES, function(RegisterElementTableAttributesEvent $event) {
            $event->tableAttributes['usage'] = [
                'label' => Craft::t('assetusage', 'Usage'),
            ];
        });

        Event::on(Asset::class, Asset::EVENT_SET_TABLE_ATTRIBUTE_HTML, function(SetElementTableAttributeHtmlEvent $event) {
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
