<?php

namespace born05\assetusage\services;

use Craft;
use craft\base\Component;
use craft\db\Query;
use craft\db\Table;
use craft\elements\Asset as AssetElement;
use craft\helpers\ElementHelper;
use born05\assetusage\Plugin;

class Asset extends Component
{
    /**
     * Count the number of times an asset is used and return a formatted string.
     * e.g. Used {count} times
     *
     * @param  AssetElement $asset
     * @return string
     */
    public function getUsageCount(AssetElement $asset): string
    {
        $relations = $this->queryRelations($asset);

        if (Plugin::getInstance()->settings->includeRevisions) {
            return $this->formatResults(count($relations));
        }

        $count = count(array_filter($relations, function ($relation) {
            try {
                /** @var craft\base\Element */
                $element = Craft::$app->elements->getElementById($relation['id'], null, $relation['siteId']);

                return !!$element && !ElementHelper::isDraftOrRevision($element);
            } catch (\Throwable $e) {
                return false;
            }
        }));

        return $this->formatResults($count);
    }

    /**
     * Get all elements related to the asset.
     *
     * @param  AssetElement $asset
     * @return array
     */
    public function getUsedIn(AssetElement $asset): array
    {
        $relations = $this->queryRelations($asset);

        $elements = [];

        foreach ($relations as $relation) {
            try {
                /** @var craft\services\Elements */
                $elementsService = Craft::$app->elements;

                /** @var craft\base\Element */
                $element = $elementsService->getElementById($relation['id'], null, $relation['siteId']);

                $root = ElementHelper::rootElement($element);
                $isRevision = $root->getIsDraft() || $root->getIsRevision();

                if ($root && !$isRevision) {
                    $elements[$root->id] = $root;
                }
            } catch (\Throwable $e) {
                // let it slide...
            }
        }

        return array_values($elements);
    }

    private function queryRelations(AssetElement $asset): array
    {
        return (new Query())
            ->select(['sourceId as id', 'sourceSiteId as siteId'])
            ->from(Table::RELATIONS)
            ->where(['targetId' => $asset->id])
            ->all();
    }

    /**
     * Format the count into a string.
     * e.g. Used {count} times
     *
     * @param  int $count
     * @return string
     */
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
