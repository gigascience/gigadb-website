<?php

class m190408_072505_add_column_contribution_to_author_table extends CDbMigration
{
	public function up()
	{
	    $this->execute('ALTER TABLE "author"
ADD "contribution_id" integer NULL;');
	}

	public function down()
	{
		echo "m190408_072505_add_column_contribution_to_author_table does not support migration down.\n";
		return false;
	}
}