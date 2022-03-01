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
        Yii::log("Status Action ...","warning");
        $remotePath = "/images/datasets/new_no_image.png";
        $localImage = Yii::$app->localStore->read("no_image.png");
        Yii::$app->cloudStore->put($remotePath, $localImage, [
            'visibility' => AdapterInterface::VISIBILITY_PUBLIC
        ]);
        $remoteImage = Yii::$app->cloudStore->read($remotePath);
        $this->getController()->renderFile($remoteImage);
    }
}

?>