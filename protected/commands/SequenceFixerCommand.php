<?php

use \yii\console\ExitCode;

/**
 * Class SequenceFixerCommand
 * Run SQL queries to fix out-of-sync sequences
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class SequenceFixerCommand extends CConsoleCommand {

    /**
     * sequencefixer fixAll
     *
     * Fix out-of-sync PostgreSQL's sequence for nextval values for all tables
     * that use id as primary key and have a sequence defined for them.
     *
     * @param $args
     * @return int
     */
    public function actionFixAll($args) {
        $ok = true;
        foreach (Yii::app()->db->schema->tables as $table) {
            if ($table->name === "YiiSession" || $table->primaryKey !== 'id' || !$table->sequenceName) {
                continue;
            }
            echo "Reset the sequence for: ".$table->name."\n";
            $sql = "SELECT setval(pg_get_serial_sequence('{$table->name}', 'id'), coalesce(max(id),0) + 1, false) FROM {$table->name}";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (CDbException $e) {
                $ok = false;
                continue;
            }

        }
        if($ok)
            return ExitCode::OK;
        return ExitCode::IOERR;
    }
}