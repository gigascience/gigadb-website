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
        echo "[$command] * $message " . PHP_EOL ;
    }
}
