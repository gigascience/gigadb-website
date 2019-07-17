<?php

class m190717_081514_update_contributions extends CDbMigration
{
	public function up()
	{
        $this->execute('DROP SEQUENCE IF EXISTS contribution_id_seq CASCADE');
        $this->execute('CREATE SEQUENCE contribution_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 4 CACHE 1;');
        $this->execute("ALTER TABLE \"contribution\"
          ALTER \"id\" SET DEFAULT nextval('contribution_id_seq');");

        $this->execute("UPDATE \"contribution\" SET \"name\" = 'Conceptualization' WHERE \"name\" = 'Contribution1';");
	    $this->execute("UPDATE \"contribution\" SET \"name\" = 'Data curation' WHERE \"name\" = 'Contribution2';");
	    $this->execute("UPDATE \"contribution\" SET \"name\" = 'Formal analysis' WHERE \"name\" = 'Contribution3';");

	    $this->execute("INSERT INTO \"contribution\" (\"name\", \"source\", \"description\")
                VALUES ('Funding acquisition', 'Funding acquisition', 'Funding acquisition');");
        $this->execute("INSERT INTO \"contribution\" (\"name\", \"source\", \"description\")
                VALUES ('Investigation', 'Investigation', 'Investigation');");
        $this->execute("INSERT INTO \"contribution\" (\"name\", \"source\", \"description\")
                VALUES ('Methodology', 'Methodology', 'Methodology');");
        $this->execute("INSERT INTO \"contribution\" (\"name\", \"source\", \"description\")
                VALUES ('Project administration', 'Project administration', 'Project administration');");
        $this->execute("INSERT INTO \"contribution\" (\"name\", \"source\", \"description\")
                VALUES ('Resources', 'Resources', 'Resources');");
        $this->execute("INSERT INTO \"contribution\" (\"name\", \"source\", \"description\")
                VALUES ('Software', 'Software', 'Software');");
        $this->execute("INSERT INTO \"contribution\" (\"name\", \"source\", \"description\")
                VALUES ('Supervision', 'Supervision', 'Supervision');");
        $this->execute("INSERT INTO \"contribution\" (\"name\", \"source\", \"description\")
                VALUES ('Validation', 'Validation', 'Validation');");
        $this->execute("INSERT INTO \"contribution\" (\"name\", \"source\", \"description\")
                VALUES ('Visualization', 'Visualization', 'Visualization');");
        $this->execute("INSERT INTO \"contribution\" (\"name\", \"source\", \"description\")
                VALUES ('Writing – original draft', 'Writing – original draft', 'Writing – original draft');");
        $this->execute("INSERT INTO \"contribution\" (\"name\", \"source\", \"description\")
                VALUES ('Writing – review & editing', 'Writing – review & editing', 'Writing – review & editing');");
	}

	public function down()
	{
		echo "m190717_081514_update_contributions does not support migration down.\n";
		return false;
	}
}