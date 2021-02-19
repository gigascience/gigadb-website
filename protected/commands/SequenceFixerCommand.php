<?php

/**
 * Class SequenceFixerCommand
 * Run SQL queries to fix out-of-sync sequences
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class SequenceFixerCommand extends CConsoleCommand {

    public function actionFixCurationLog($args) {
        echo "Reset the sequence for curation_log".PHP_EOL;
        $sql = "SELECT setval(pg_get_serial_sequence('curation_log', 'id'), coalesce(max(id),0) + 1, false) FROM curation_log";
        Yii::app()->db->createCommand($sql)->execute();

        return 0;
    }
}