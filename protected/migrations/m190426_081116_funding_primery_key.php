<?php

class m190426_081116_funding_primery_key extends CDbMigration
{
	public function up()
	{
	    $this->execute("ALTER TABLE \"funding\"
ADD CONSTRAINT \"funding_id\" PRIMARY KEY (\"id\");");
	}

	public function down()
	{
        $this->execute("ALTER TABLE \"funding\"
DROP CONSTRAINT \"funding_id\";");
	}
}