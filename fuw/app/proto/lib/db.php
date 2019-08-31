<?php
	/**
	 * Connect to the database
	 *
	 * @return object return a database connection handle
	 */
	function connectDB(): object
	{
		$appconfig = parse_ini_file("/app/proto/appconfig.ini");

		$db_user = $appconfig["db_user"];
		$db_password = $appconfig["db_password"];
		$db_source = $appconfig["db_source"];

		$dbh = new PDO("pgsql:host=$db_host;dbname=$db_source", "$db_user", "$db_password");
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); //PHP warnings for SQL errors
		return $dbh ;
	}
?>