<?php

$this->truncateTable("file_sample");
$this->truncateTable("species");
$this->truncateTable("sample");

$this->loadFixture("species");


?>
