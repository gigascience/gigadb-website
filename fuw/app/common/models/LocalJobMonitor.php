<?php

namespace common\models;

use \Yii;

use \zhuravljov\yii\queue\monitor\JobMonitor;

/**
 * subclass JobMonitor to get around error thrown by superclass's getSenderName()
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 **/
class LocalJobMonitor extends JobMonitor
{
	/**
     * @param JobEvent $event
     * @throws
     * @return string
     */
    protected function getSenderName($event)
    {
        foreach (Yii::$app->getComponents(false) as $id => $component) {
            if ($component === $event->sender) {
                return $id;
            }
            elseif(get_class($component) === "yii\queue\beanstalk\Queue" && $component->tube === $event->sender->tube) {
            	return $id;
            }
        }
        throw new InvalidConfigException('Queue must be an application component.');
    }

}