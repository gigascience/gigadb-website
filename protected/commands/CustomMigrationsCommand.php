<?php
/**
 * Command to drop all constraints and indexes
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CustomMigrationsCommand extends CConsoleCommand
{

    public function getHelp()
    {
        $helpText = "drop constraints and indexes in the database" . PHP_EOL;
        $helpText .= "Usage: ./protected/yiic custommigration dropconstraints" . PHP_EOL;
        $helpText .= "Usage: ./protected/yiic custommigration dropindexes" . PHP_EOL;
        return $helpText;
    }

    public function actionDropConstraints() {
        $sql =<<<END
SELECT nspname,relname, conname
 FROM pg_constraint 
 INNER JOIN pg_class ON conrelid=pg_class.oid 
 INNER JOIN pg_namespace ON pg_namespace.oid=pg_class.relnamespace 
 ORDER BY CASE WHEN contype='f' THEN 0 ELSE 1 END,contype,nspname,relname,conname;
END;

        $dropCommands = [];
        try {
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($rows as $row) {
                $dropCommands[]= "ALTER TABLE \"{$row['nspname']}\".\"{$row['relname']}\" DROP CONSTRAINT IF EXISTS {$row['conname']}";
            }
        } catch (CDbException $e) {
            Yii::log($e->getMessage(),"error");
            return 1;
        }

        try {
            foreach ($dropCommands as $instruction) {
                echo "About to execute: $instruction".PHP_EOL;
                Yii::app()->db->createCommand($instruction)->execute();
            }
        }
        catch(CDbException $e) {
            Yii::log($e->getMessage(),"error");
            return 1;
        }

    }

    public function actionDropIndexes() {
        $sql =<<<END
SELECT nspname, relname 
FROM pg_index 
INNER JOIN pg_class ON indexrelid=pg_class.oid 
INNER JOIN pg_namespace ON pg_namespace.oid=pg_class.relnamespace WHERE indisprimary=FALSE and indisvalid=TRUE AND nspname='public' ORDER BY nspname,relname;
END;

        $dropCommands = [];
        try {
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($rows as $row) {
                $dropCommands[]= "DROP INDEX  \"{$row['nspname']}\".\"{$row['relname']}\" RESTRICT;";
            }
        } catch (CDbException $e) {
            Yii::log($e->getMessage(),"error");
            return 1;
        }

        try {
            foreach ($dropCommands as $instruction) {
                echo "About to execute: $instruction".PHP_EOL;
                Yii::app()->db->createCommand($instruction)->execute();
            }
        }
        catch(CDbException $e) {
            Yii::log($e->getMessage(),"error");
            return 1;
        }

    }


}