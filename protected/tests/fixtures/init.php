<?php
	foreach($this->getFixtures() as $tableName=>$fixturePath)
	{
		print_r("\nresetting: $tableName\n");
		$this->resetTable($tableName);
		//$this->loadFixture($tableName);
	}
	$this->loadFixture("author");
	$this->loadFixture("dataset");
	$this->loadFixture("dataset_author");

?>