<?php

class CUnitTestPgsqlSchema extends CPgsqlSchema
{
	public function checkIntegrity($check=true,$schema='')
        {
                $db = $this->getDbConnection();
                if ($check) {
                    $db->createCommand("SET CONSTRAINTS ALL DEFERRED")->execute();
                } else {
                    $db->createCommand("SET CONSTRAINTS ALL IMMEDIATE")->execute();
                }
        }

}


?>