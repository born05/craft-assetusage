<?php
namespace Craft;

class AssetUsagePlugin extends BasePlugin
{
    public function getName()
    {
        return Craft::t('Asset Usage');
    }

    public function getVersion()
    {
        return '1.0.1';
    }

    public function getDeveloper()
    {
        return 'Born05';
    }

    public function getDeveloperUrl()
    {
        return 'http://www.born05.com/';
    }

    /**
     * @return string
     */
    public function getPluginUrl()
    {
        return 'https://github.com/born05/craft-assetusage';
    }
    /**
     * @return string
     */
    public function getDocumentationUrl()
    {
        return $this->getPluginUrl() . '/blob/master/README.md';
    }

    public function getReleaseFeedUrl()
    {
        return 'https://raw.githubusercontent.com/born05/craft-assetusage/master/releases.json';
    }

    public function hasCpSection()
    {
        return false;
    }

    /**
     * Adds the following attributes to the AssetFileModel fields in CMS
     * NOTE: You still need to select them with the 'gear'
     *
     * @return array
     */
    public function defineAdditionalAssetTableAttributes()
    {
        return array(
            'usage' => Craft::t('Usage'),
        );
    }

    /**
     * Returns the content for the additional attributes field
     *
     * @param AssetFileModel $asset
     * @param string $attribute
     * @return string The content for the field
     */
    public function getAssetTableAttributeHtml(AssetFileModel $asset, $attribute)
    {
        if ($attribute == 'usage') {
            return craft()->assetUsage_asset->getUsage($asset);
        }
    }
}
