<?php

namespace tests\functional;

use app\components\DatasetFilesUpdater;

class ReplaceFileUrlSubstringWithPrefixCest
{
    public function tryReplaceFileUrlSubstringWithPrefix(\FunctionalTester $I): void
    {
        $dfu = DatasetFilesUpdater::build(true);
        $success = $dfu->replaceFileUrlSubstringWithPrefix('100142', '/pub/', 'https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live');
    }
}
