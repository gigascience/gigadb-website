<?php
Class LoggableCommandBehavior extends CBehavior
{

    public function init() {

        // Yii::getLogger()->autoFlush = 1;
        // Yii::getLogger()->autoDump = true;

        set_error_handler( array($this, "error") );
        register_shutdown_function(array($this, "handle_fatal_error"));

        parent::init();

    }

    public function log($message) {
        //Yii::log($message, "info","application.commands." . $command) ;
        echo "[". get_class($this->owner) ."] * $message " . PHP_EOL ;
    }

    public function error($num, $str, $file, $line, $context = null) {
        echo "[". get_class($this->owner) ."] * A PHP Error $num occured in " . $file  ."[" . $line . "]: $str". PHP_EOL;
    }

    public function handle_fatal_error() {
        $error = error_get_last();
        if ( $error["type"] == E_ERROR ) {
            $this->log("PHP Fatal Error (E_ERROR)  occured: ") ;
            $this->error( $error["type"], $error["message"], $error["file"], $error["line"] );
            if (isset($this->owner->queue) && isset($this->owner->current_job)) {
                $this->owner->queue->bury($this->owner->current_job['id'], 0);
            }
        }
    }

}
