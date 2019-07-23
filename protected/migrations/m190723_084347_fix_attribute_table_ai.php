<?php

class m190723_084347_fix_attribute_table_ai extends CDbMigration
{
	public function up()
	{
        $this->execute('DROP SEQUENCE IF EXISTS attribute_id_seq CASCADE;');
        $this->execute('CREATE SEQUENCE attribute_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;');
        $this->execute('ALTER TABLE "attribute" ALTER "id" SET DEFAULT nextval(\'attribute_id_seq\');');
        $this->execute('select setval(\'attribute_id_seq\',  (SELECT MAX(id) FROM attribute));');
	}

	public function down()
	{
		echo "m190723_084347_fix_attribute_table_ai does not support migration down.\n";
		return false;
	}
}