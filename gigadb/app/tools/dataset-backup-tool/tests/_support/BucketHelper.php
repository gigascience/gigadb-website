<?php
namespace Codeception\Module;
//namespace Helper;
//
//use Throwable;
//use Yii;
//use \yii\helpers\Console;
//use \yii\console\Controller;
//use \yii\console\ExitCode;

/**
 * @method stdout(string $string, int $FG_RED)
 */
class BucketHelper extends \Codeception\Module
{
    public function _beforeSuite($settings = array())
    {
        $this->debug("********** BEFORE *********");
        try {
            $output = shell_exec("scripts/create_bucket.sh");
        }
        catch (Throwable $e) {
            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::OSERR;
        }
        return $output;
    }

    public function _afterSuite()
    {
        $this->debug("********** AFTER *********");
        try {
            $output = shell_exec("coscmd -c ./scripts/.cos.conf delete -r -f dataset/ 2>&1");
            $output = shell_exec("scripts/delete_bucket.sh");
        }
        catch (Throwable $e) {
            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::OSERR;
        }
        return $output;
    }
}