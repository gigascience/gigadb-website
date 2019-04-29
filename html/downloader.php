<?php
	$thisurl = parse_url($_SERVER['REQUEST_URI']);
	parse_str($thisurl["query"], $params);
	// echo $params["d"];

	/**
	 * the file class
	 * id | doi_suffix | name | size | status | location | description | initial_md5 | format | data_type | created_at | updated_at
	 */
	class File {
	    public $id;
	    public $doi_suffix;
	    public $name;
	    public $size;
	    public $status;
	    public $location;
	    public $description;
	    public $initial_md5;
	    public $format;
	    public $data_type;
	    public $created_at;
	    public $updated_at;
	}
	/**
	 * Connect to the database
	 *
	 * @return object return a database connection handle
	 */
	function connectDB(): object
	{
		$dbh = new PDO('pgsql:host=tus-uppy-proto_database_1;dbname=proto', 'proto', 'proto');
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); //PHP warnings for SQL errors
		return $dbh ;
	}

	/**
	 * Return the list of file and metadata for a given dataset
	 *
	 * @param object $dbh database handle
	 * @param int $dataset DOI suffix
	 * @return array list of files
	 */
	function getFileTable(object $dbh, int $dataset): array
	{
		$sql = "select * from file where doi_suffix= ? and status = 'uploading'";
		$st = $dbh->prepare($sql);
		$st->bindParam(1, $dataset);
		$st->execute();
		return $st->fetchAll(PDO::FETCH_CLASS, "File");
	}

	$dbh = connectDB();
	$table =  getFileTable($dbh, $params["d"]);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Prototype for File Upload Wizard (Mockup)</title>
</head>
<body>
	<h1>Mockup for dataset <?= $params["d"]?> </h1>

	<table border="1">
		<tr>
    		<th>id</th>
    		<th>doi_suffix</th>
    		<th>name</th>
    		<th>size</th>
    		<th>status</th>
    		<th>location</th>
    		<th>description</th>
    		<th>initial_md5</th>
    		<th>format</th>
    		<th>data_type</th>
    		<th>created_at</th>
    		<th>updated_at</th>
        </tr>
        <? foreach ($table as $file) { ?>
        	<tr>
	        	<td><?= $file->id ?></td>
	        	<td><?= $file->doi_suffix ?></td>
	        	<td><?= $file->name ?></td>
	        	<td><?= $file->size ?></td>
	        	<td><?= $file->status ?></td>
	        	<td><a href="<?= $file->location ?>"><?= $file->location ?></a></td>
	        	<td><?= $file->description ?></td>
	        	<td><?= $file->initial_md5 ?></td>
	        	<td><?= $file->format ?></td>
	        	<td><?= $file->data_type ?></td>
	        	<td><?= $file->created_at ?></td>
	        	<td><?= $file->updated_at ?></td>
	        </tr>
        <? } ?>
	</table>
</body>
</html>