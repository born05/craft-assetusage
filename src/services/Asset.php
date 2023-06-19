<?php

namespace born05\assetusage\services;

use born05\assetusage\Plugin;
use Craft;
use craft\base\Component;
use craft\db\Query;
use craft\db\Table;
use craft\elements\Asset as AssetElement;

class Asset extends Component
{
    /**
     * Count the number of times an asset is used and return a formatted string.
     * e.g. Used {count} times
     *
     * @param  AssetElement $asset
     * @return string
     */
    public function getUsage(AssetElement $asset): string
    {
        $results = (new Query())
            ->select(['sourceId', 'sourceSiteId'])
            ->from(Table::RELATIONS)
            ->where(['targetId' => $asset->id])
            ->all();

        if (Plugin::getInstance()->settings->includeRevisions) {
            $elementIds = [];

            /** @var craft\services\Elements */
            $elementsService = Craft::$app->elements;

            foreach ($results as $result) {
                $element = $elementsService->getElementById($result['sourceId'], null, $result['sourceSiteId']);

                if (isset($element)) {
                    $currentRevision = $element->getCurrentRevision();

                    if (!isset($currentRevision) || $currentRevision->id === $element->id) {
                        $elementIds[] = $element->id;
                    }
                }
            }

            $count = count($elementIds);
        } else {
            $count = count($results);
        }

        return $this->formatResults($count);
    }

    private function formatResults($count): string
    {
        if ($count === 1) {
            return Craft::t('assetusage', 'Used {count} time', ['count' => $count]);
        } elseif ($count > 1) {
            return Craft::t('assetusage', 'Used {count} times', ['count' => $count]);
        }

        return '<span style="color: #da5a47;">' . Craft::t('assetusage', 'Unused') . '</span>';
    }
}
