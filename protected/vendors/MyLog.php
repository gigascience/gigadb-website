<?
class MyLog {
    public static function debug($str) {
        $bt = debug_backtrace();
        $caller = $bt[1];
        $class = $caller['class'];
        $function = $caller['function'];
        $line = $caller['line'];
        Yii::log("$class::$function($line)> $str", 'debug');
    }

    public static function warning($str) {
        $bt = debug_backtrace();
        $caller = $bt[1];
        $class = $caller['class'];
        $function = $caller['function'];
        $line = $caller['line'];
        Yii::log("$class::$function($line)> $str", 'warning');
    }

    public static function error($str) {
        $bt = debug_backtrace();
        $caller = $bt[1];
        $class = $caller['class'];
        $function = $caller['function'];
        $line = $caller['line'];
        Yii::log("$class::$function($line)> $str", 'error');
    }

    public static function dump($str, $data, $level='debug') {
        $bt = debug_backtrace();
        $caller = $bt[1];
        $class = $caller['class'];
        $function = $caller['function'];
        $line = $caller['line'];
        Yii::log("$class::$function($line)> $str" . print_r($data, true), $level);
    }

    # Log error saving model
    public static function saveError($model, $str='') {
        $bt = debug_backtrace();
        $caller = $bt[1];
        $class = $caller['class'];
        $function = $caller['function'];
        $line = $caller['line'];
        if (!$str) {
            $str = "Error saving model {$model->id}: ";
        }
        Yii::log("$class::$function($line)> $str " . print_r($model->getErrors(), true), 'error');
    }

    public static function saveDebug($model, $str='') {
        $bt = debug_backtrace();
        $caller = $bt[1];
        $class = $caller['class'];
        $function = $caller['function'];
        $line = $caller['line'];
        if (!$str) {
            $str = "Error saving model {$model->id}: ";
        }
        Yii::log("$class::$function($line)> $str " . print_r($model->getErrors(), true), 'debug');
    }
}
?>
