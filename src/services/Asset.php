<?php

namespace born05\assetusage\services;

use Craft;
use craft\base\Component;
use craft\db\Query;
use craft\elements\Asset as AssetElement;

class Asset extends Component
{
    private $usedAssetIds = null;

    /**
     * Determines if an asset is in use or not.
     *
     * @param  AssetElement $asset
     * @return string
     */
    public function getUsage(AssetElement $asset)
    {
        $results = (new Query())
          ->select(['id'])
          ->from(['{{%relations}}'])
          ->where(['targetId' => $asset->id])
          ->orWhere(['sourceId' => $asset->id])
          ->all();
          
        $count = count($results);

        if ($count === 1) {
            return Craft::t('assetusage', 'Used {count} time', [ 'count' => $count ]);
        } else if ($count > 1) {
            return Craft::t('assetusage', 'Used {count} times', [ 'count' => $count ]);
        }

        return '<span style="color: #da5a47;">' . Craft::t('assetusage', 'Unused') . '</span>';
    }
}
