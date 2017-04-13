<?php
Class LoggableCommandBehavior extends CBehavior
{

    public function init() {

        // Yii::getLogger()->autoFlush = 1;
        // Yii::getLogger()->autoDump = true;
        parent::init();

    }

    public function log_setup() {
        $this->log("Configure logging") ;
        set_error_handler( array($this, "error") );
        register_shutdown_function( array($this, "handle_fatal_error") );
    }

    public function log($message) {
        //Yii::log($message, "info","application.commands." . $command) ;

        $job_id = isset($this->owner->current_job) ? "(" . $this->owner->current_job['id'] . ")" : false ;
        date_default_timezone_set('Asia/Hong_Kong');
        $time_index = date(DATE_ATOM);

        echo "[". $time_index ."]" . "[". get_class($this->owner) ."] * $job_id $message " . PHP_EOL ;
    }

    public function error($num, $str, $file, $line, $context = null) {

        // $job_id = isset($this->owner->current_job) ? "(" . $this->owner->current_job['id'] . ")" : false ;
        // date_default_timezone_set('Asia/Hong_Kong');
        // $time_index = date(DATE_ATOM);
        //
        // echo "[". $time_index ."]" . "[". get_class($this->owner) ."] * $job_id A PHP Error $num occured in " . $file  ."[" . $line . "]: $str". PHP_EOL;
        if (E_ERROR === $num) {
            throw new Exception ("A PHP Error $num occured in " . $file  ."[" . $line . "]: $str", $num) ;
        }
        else {
            $this->log("A PHP Error $num was occured in " . $file  ."[" . $line . "]: $str");
        }
    }

    public function handle_fatal_error() {

        restore_error_handler();
        $error = error_get_last();
        if ( $error["type"] === E_ERROR ) {
            $this->log("PHP Fatal Error (E_ERROR)  occured in " . $error["file"]  ."[" . $error["line"] . "]: " . $error["message"]) ;
            if (isset($this->owner->queue) && isset($this->owner->current_job)) {
                $this->owner->queue->bury($this->owner->current_job['id'], 0);
                $this->owner->current_job = null;
            }
        }
        else if ( $error["type"] === E_NOTICE ) {
            $this->log("PHP Fatal Error (E_NOTICE)  occured: ") ;
            if (isset($this->owner->queue) && isset($this->owner->current_job)) {
                $this->owner->queue->bury($this->owner->current_job['id'], 0);
                $this->owner->current_job = null;
            }
        }
        else {
            $this->log("A PHP Fatal Error occured") ;
        }
    }

}
