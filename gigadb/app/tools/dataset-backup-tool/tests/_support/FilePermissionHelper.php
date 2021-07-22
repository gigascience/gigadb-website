<?php
namespace Codeception\Module;

class FilePermissionHelper extends \Codeception\Module
{
    public function _beforeSuite($settings = array())
    {
        $this->debug("********** BEFORE *********");
        try {
            $output = shell_exec("scripts/perm_to_not_ok.sh");
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
            $output = shell_exec("scripts/perm_to_ok.sh");
        }
        catch (Throwable $e) {
            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::OSERR;
        }
        return $output;
    }
}