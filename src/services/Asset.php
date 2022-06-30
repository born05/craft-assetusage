<?php

namespace born05\assetusage\services;

use Craft;
use craft\base\Component;
use craft\db\Query;
use craft\db\Table;
use craft\elements\Asset as AssetElement;

class Asset extends Component
{
    /**
     * Determines if an asset is in use or not.
     *
     * @param  AssetElement $asset
     * @return string
     */
    public function getUsage(AssetElement $asset)
    {
        $results = (new Query())
          ->select(['sourceId'])
          ->from(Table::RELATIONS)
          ->where(['targetId' => $asset->id])
          ->column();

        $count = count($results);

        return $this->formatResults($count);
    }

    /**
     * Determines if an asset is in use or not, ignoring revisions.
     *
     * @param  AssetElement $asset
     * @return string
     */
    public function getCurrentUsage(AssetElement $asset)
    {
        $results = (new Query())
          ->select(['sourceId', 'sourceSiteId'])
          ->from(Table::RELATIONS)
          ->where(['targetId' => $asset->id])
          ->all();

        $elementIds = [];
        foreach ($results as $result) {
            $element = Craft::$app->elements->getElementById($result['sourceId'], null, $result['sourceSiteId']);

            if (isset($element)) {
                $currentRevision = $element->getCurrentRevision();

                if (!isset($currentRevision) || $currentRevision->id === $element->id) {
                    $elementIds[] = $element->id;
                }
            }
        }

        $count = count($elementIds);

        return $this->formatResults($count);
        return "lol";
    }

    private function formatResults($count)
    {
        if ($count === 1) {
            return Craft::t('assetusage', 'Used {count} time', [ 'count' => $count ]);
        } elseif ($count > 1) {
            return Craft::t('assetusage', 'Used {count} times', [ 'count' => $count ]);
        }

        return '<span style="color: #da5a47;">' . Craft::t('assetusage', 'Unused') . '</span>';
    }
}
