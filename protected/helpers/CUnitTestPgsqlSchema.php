<?php

class CUnitTestPgsqlSchema extends CPgsqlSchema
{
	public function checkIntegrity($check=true,$schema='')
        {
        		if('' == $schema) {
        			# this is current or default schema
        		}

                $db = $this->getDbConnection();
                if ($check) {
                    $db->createCommand("SET CONSTRAINTS ALL DEFERRED")->execute();
                } else {
                    $db->createCommand("SET CONSTRAINTS ALL IMMEDIATE")->execute();
                }
        }

}


?>