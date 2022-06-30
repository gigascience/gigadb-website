<?php

namespace common\models;

use \Yii;
use yii\queue\Queue;


class EMReportJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{

    public string $content;
    public string $effectiveDate ;
    public string $fetchDate ;
    public string $scope;

    public function execute($queue)
    {
        // TODO: Implement execute() method.
    }
}

