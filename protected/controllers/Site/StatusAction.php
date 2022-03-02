<?php

use League\Flysystem\AdapterInterface;
use Yii;
/**
 * This action for DatasetController is for internal use, display status info about flysystem configuration
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class StatusAction extends CAction
{
    public function run()
    {
        Yii::log("Testing Flysystem configuration","warning");
        $remotePath = "/live/images/datasets/new_no_image.png";
        $localImage = Yii::$app->localStore->read("no_image.png");
        assert($localImage !== null && $localImage !== false);
        Yii::$app->cloudStore->put($remotePath, $localImage, [
            'visibility' => AdapterInterface::VISIBILITY_PUBLIC
        ]);
        $remoteImage = Yii::$app->cloudStore->read($remotePath);

        header("Content-type: image/png");
        echo $remoteImage;
        ob_flush();
    }
}

?>