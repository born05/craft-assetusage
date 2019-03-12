<?php
namespace Craft;

class AssetUsage_AssetService extends BaseApplicationComponent
{
    /**
     * Determines if an asset is in use or not.
     *
     * @param  AssetFileModel $asset
     * @return string
     */
    public function getUsage(AssetFileModel $asset)
    {

        // Use fast query instead of memory consuming method
        $results = craft()->db->createCommand()
            ->select(['id'])
            ->from(['{{relations}}'])
            ->where(['targetId' => $asset->id])
            ->orWhere(['sourceId' => $asset->id])
            ->queryAll();

        $count = count($results);

        if ($count === 1) {
            return Craft::t('assetusage', 'Used {count} time', [ 'count' => $count ]);
        } else if ($count > 1) {
            return Craft::t('assetusage', 'Used {count} times', [ 'count' => $count ]);
        }
        
        return '<span style="color: #da5a47;">' . Craft::t('assetusage', 'Unused') . '</span>';
    }
}
