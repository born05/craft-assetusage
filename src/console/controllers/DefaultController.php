<?php

namespace born05\assetusage\console\controllers;

use Craft;
use craft\db\Query;
use craft\db\Table;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Controls
 */
class DefaultController extends Controller
{
    /**
     * Lists all unused assets.
     */
    public function actionListUnused()
    {
        echo "Listing all unused asset ids:\n";

        $results = $this->getUnusedAssets();
        foreach ($results as $result) {
            echo $result['id'] . ' : ' . $result['filename'] . "\n";
        }

        return "Done.";
    }

    /**
     * Deletes all unused assets.
     */
    public function actionDeleteUnused()
    {
        echo "Deleting all unused asset ids:\n";

        $assets = Craft::$app->getAssets();

        $results = $this->getUnusedAssets();
        $assetCount = count($results);

        if ($this->confirm("Delete $assetCount assets?")) {
            foreach ($results as $result) {
                echo 'Deleting ' . $result['id'] . ' : ' . $result['filename'] . "\n";

                $asset = $assets->getAssetById($result['id']);
                if ($asset) {
                    Craft::$app->getElements()->deleteElement($asset);
                }
            }

            return "Done.";
        }
    }

    private function getUnusedAssets()
    {
        $subQuery = (new Query())
          ->select('id')
          ->from(Table::RELATIONS . ' relations')
          ->where('relations.targetId=assets.id')
          ->orWhere('relations.sourceId=assets.id');
    
        return (new Query())
          ->select(['id', 'filename'])
          ->from(Table::ASSETS . ' assets')
          ->where(['not exists', $subQuery])
          ->all();
    }
}
