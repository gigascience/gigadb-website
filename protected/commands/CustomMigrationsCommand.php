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
        $helpText .= "Usage: ./protected/yiic custommigration droptriggers" . PHP_EOL;
        $helpText .= "Usage: ./protected/yiic custommigration refreshmaterializedviews" . PHP_EOL;
        $helpText .= "Usage: ./protected/yiic custommigration preparedropcreateconstraints" . PHP_EOL;
        return $helpText;
    }

    /**
     * @return int
     * @throws CException
     */
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

    /**
     * @return int
     * @throws CException
     */
    public function actionDropIndexes() {
        $sql =<<<END
SELECT nspname, relname 
FROM pg_index 
INNER JOIN pg_class ON indexrelid=pg_class.oid 
INNER JOIN pg_namespace ON pg_namespace.oid=pg_class.relnamespace WHERE indisprimary=FALSE and indisvalid=TRUE AND nspname='public' 
ORDER BY nspname,relname;
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

    /**
     * @return int
     * @throws CException
     */
    public function actionDropTriggers() {
        $sql =<<<END
SELECT distinct trigger_name, event_object_table
FROM information_schema.triggers
WHERE trigger_schema = 'public';
END;

        $dropCommands = [];
        try {
            $rows = Yii::app()->db->createCommand($sql)->queryAll();
            foreach ($rows as $row) {
                $dropCommands[]= "DROP TRIGGER  {$row['trigger_name']} ON {$row['event_object_table']} RESTRICT;";
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

    public function actionRefreshMaterializedViews()
    {
        Yii::app()->db->createCommand("refresh materialized view file_finder")->execute();
        Yii::app()->db->createCommand("refresh materialized view sample_finder")->execute();
        Yii::app()->db->createCommand("refresh materialized view dataset_finder")->execute();
    }

    public function actionPrepareDropCreateConstraints()
    {
        $dropConstraintsQuery = "";
        $addConstraintsQuery = "";
        $dropIndexQuery = "";
        $addIndexQuery = "";
        $dropTriggerQuery = "";
        $addTriggerQuery = "";

        $rows = Yii::app()->db->createCommand("SELECT 'ALTER TABLE '||nspname||'.\"'||relname||'\" DROP CONSTRAINT \"'||conname||'\";' as q
FROM pg_constraint
INNER JOIN pg_class ON conrelid=pg_class.oid
INNER JOIN pg_namespace ON pg_namespace.oid=pg_class.relnamespace
ORDER BY CASE WHEN contype='f' THEN 0 ELSE 1 END,contype,nspname,relname,conname;")->queryAll();

        foreach ($rows as $row) {
           $dropConstraintsQuery .= $row["q"].PHP_EOL;
        }
        file_put_contents("/var/www/protected/runtime/dropConstraintsQuery.sql", $dropConstraintsQuery);

        $rows = Yii::app()->db->createCommand("SELECT 'ALTER TABLE '||nspname||'.\"'||relname||'\" ADD CONSTRAINT \"'||conname||'\" '|| pg_get_constraintdef(pg_constraint.oid)||';' as q
FROM pg_constraint
INNER JOIN pg_class ON conrelid=pg_class.oid
INNER JOIN pg_namespace ON pg_namespace.oid=pg_class.relnamespace
ORDER BY CASE WHEN contype='f' THEN 0 ELSE 1 END DESC,contype DESC,nspname DESC,relname DESC,conname DESC;")->queryAll();

        foreach ($rows as $row) {
            $addConstraintsQuery .= $row["q"].PHP_EOL;
        }
        file_put_contents("/var/www/protected/runtime/addConstraintsQuery.sql", $addConstraintsQuery);

        $rows = Yii::app()->db->createCommand("SELECT 'DROP INDEX ' || indexname || ';' as q FROM pg_indexes WHERE schemaname = 'public'")->queryAll();
        foreach ($rows as $row) {
            $dropIndexQuery .= $row["q"].PHP_EOL;
        }
        file_put_contents("/var/www/protected/runtime/dropIndexQuery.sql", $dropIndexQuery);

        $rows = Yii::app()->db->createCommand("SELECT indexdef || ';' as q FROM pg_indexes WHERE schemaname = 'public'")->queryAll();
        foreach ($rows as $row) {
            $addIndexQuery .= $row["q"].PHP_EOL;
        }
        file_put_contents("/var/www/protected/runtime/addIndexQuery.sql", $addIndexQuery);

        $dropTriggerQuery = "drop trigger if exists file_finder_trigger on file RESTRICT;".PHP_EOL;
        $dropTriggerQuery .=  "drop trigger if exists sample_finder_trigger on sample RESTRICT;".PHP_EOL;
        $dropTriggerQuery .=  "drop trigger if exists dataset_finder_trigger on dataset RESTRICT;".PHP_EOL;

        $addTriggerQuery = "create trigger file_finder_trigger
after insert or update or delete or truncate
on file for each statement 
execute procedure refresh_file_finder();".PHP_EOL;
        $addTriggerQuery .= "create trigger sample_finder_trigger
after insert or update or delete or truncate
on sample for each statement 
execute procedure refresh_sample_finder();".PHP_EOL;
        $addTriggerQuery .= "create trigger dataset_finder_trigger
after insert or update or delete or truncate
on dataset for each statement 
execute procedure refresh_dataset_finder();".PHP_EOL;

        file_put_contents("/var/www/protected/runtime/dropTriggerQuery.sql", $dropTriggerQuery);
        file_put_contents("/var/www/protected/runtime/addTriggerQuery.sql", $addTriggerQuery);
    }

}