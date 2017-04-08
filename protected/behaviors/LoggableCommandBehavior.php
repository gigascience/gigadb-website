<?php
Class LoggableCommandBehavior extends CBehavior
{

    public function init() {

        Yii::getLogger()->autoFlush = 1;
        Yii::getLogger()->autoDump = true;
        parent::init();

    }

    public function log($message, $command="application.commands") {
        //Yii::log($message, "info","application.commands." . $command) ;
        echo "[". get_class($this->owner) ."] * $message " . PHP_EOL ;
    }

    public function error($num, $str, $file, $line, $context = null) {
        echo "[". get_class($this->owner) ."] * A PHP Error [$num] occured in " . $file  ."[" . $line . "]: $str". PHP_EOL;
    }
}
