<?php

namespace born05\assetusage\models;

use craft\base\Model;

class Settings extends Model
{
    public bool $includeRevisions = false;
    public bool $renderUsedByInAssetDetail = true;

    public function defineRules(): array
    {
        return [
            [['includeRevisions', 'renderUsedByInAssetDetail'], 'bool'],
        ];
    }
}
