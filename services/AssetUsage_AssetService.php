<?php
namespace Craft;

class AssetUsage_AssetService extends BaseApplicationComponent
{
    private $usedAssetIds = null;

    /**
     * Determines if an asset is in use or not.
     *
     * @param  AssetFileModel $asset
     * @return string
     */
    public function getUsage(AssetFileModel $asset)
    {
        if (in_array($asset->id, $this->getUsedAssetIds())) {
            return Craft::t('Used');
        }

        return '<span style="color: #da5a47;">' . Craft::t('Unused') . '</span>';
    }

    /**
     * Retrieves all used asset ids.
     *
     * @return array
     */
    public function getUsedAssetIds()
    {
        if (is_null($this->usedAssetIds)) {
            $relatedAssetIds = array();

            $relatedAssetIds = array_merge($relatedAssetIds, $this->getAllElementsOfType(ElementType::Entry));
            $relatedAssetIds = array_merge($relatedAssetIds, $this->getAllElementsOfType(ElementType::Category));
            $relatedAssetIds = array_merge($relatedAssetIds, $this->getAllElementsOfType(ElementType::Tag));
            $relatedAssetIds = array_merge($relatedAssetIds, $this->getAllElementsOfType(ElementType::GlobalSet));
            $relatedAssetIds = array_merge($relatedAssetIds, $this->getAllElementsOfType(ElementType::MatrixBlock));

            // In case of SuperTable plugin.
            $relatedAssetIds = array_merge($relatedAssetIds, $this->getAllElementsOfType('SuperTable_Block'));

            // In case of Neo plugin
            $relatedAssetIds = array_merge($relatedAssetIds, $this->getAllElementsOfType('Neo_Block'));

            $this->usedAssetIds = array_unique($relatedAssetIds);
        }

        return $this->usedAssetIds;
    }

    /**
     * Retrieve all related asset ids.
     *
     * @param array $relatedTo
     * @return array
     */
    private function getRelatedAssetIds($relatedTo)
    {
        $criteria = craft()->elements->getCriteria(ElementType::Asset);
        $criteria->limit = null;
        $criteria->relatedTo = array(
            'sourceElement' => $relatedTo,
        );

        return $criteria->ids();
    }

    /**
     * Get all elements of type.
     * @return array
     */
    private function getAllElementsOfType($type)
    {
        $relatedAssetIds = array();

        // Make sure the type exists.
        if (craft()->elements->getElementType($type) === null) {
            return $relatedAssetIds;
        }

        $locales = craft()->i18n->getSiteLocaleIds();

        foreach ($locales as $locale) {
            $criteria = craft()->elements->getCriteria($type);
            $criteria->locale = $locale;
            $criteria->status = null;
            $criteria->limit = null;

            $entries = $criteria->find();
            $relatedAssetIds = array_merge($relatedAssetIds, $this->getRelatedAssetIds($entries));
        }

        return $relatedAssetIds;
    }
}
