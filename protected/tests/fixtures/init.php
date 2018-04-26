<?php
	foreach($this->getFixtures() as $tableName=>$fixturePath)
	{
		print_r("\nresetting: $tableName\n");
		$this->resetTable($tableName);
		//$this->loadFixture($tableName);
	}
	print_r("\loading fixture: author\n");
	$this->loadFixture("author");
	print_r("\loading fixture: dataset\n");
	$this->loadFixture("dataset");
	print_r("\loading fixture: dataset_author\n");
	$this->loadFixture("dataset_author");

?>