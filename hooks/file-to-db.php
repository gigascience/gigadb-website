#!/usr/bin/php

<?php

const FLAG_PATH = "/var/tmp/processing_flag" ;
const DATA_TYPES = array(
					"txt" => "Read Me",
					"sh" => "Script",
					"jpg" => "Photography",
					"png" => "Infographics",
					"zip" => "Mixed Archive",
					"pdf" => "Instructions",
				);

const FILE_FORMATS = array(
					"txt" => "TEXT",
					"doc" => "TEXT",
					"md" => "TEXT",
					"text" => "TEXT",
					"readme" => "TEXT",
					"fa" => "FASTA",
					"pdf" => "PDF",
);

/**
 * Determine an approximate data type based on file extension
 *
 * @param string file name
 * @return string the type of the data
 */
function getApproximateDataTypeFromFile(string $file_name): string
{

	$ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
	if (true == in_array($ext, array_keys(DATA_TYPES)) ) {
		return DATA_TYPES[$ext];
	}
	return "Unknown Data Type" ;
}

/**
 * Determine file format based on file extension
 *
 * @param string file name
 * @return string the file format
 */
function getFileFormatFromFile(string $file_name): string
{

	$ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
	if (true == in_array($ext, array_keys(FILE_FORMATS)) ) {
		return FILE_FORMATS[$ext];
	}
	return "Unknown File Format" ;
}

/**
 * Construct the ftp link
 *
 * @param string $file_name the name of the file
 * @param int $datasetid the dataset identifier associated to the file
 * @return string the ftp url
 */
function generateFTPLink(string $file_name, int $dataset): string
{
	$handle = fopen("/var/access/$dataset/download_token.txt", "r");
	$line = fgets($handle) ;
	if (true == $line) {
		$download_token = chop($line);
	}
	fclose($handle);

	$appconfig = parse_ini_file("/var/appconfig.ini");
	$ftpd_endpoint = $appconfig["ftpd_endpoint"] ?? "localhost";
	$ftp_link = "ftp://d-$dataset:$download_token@$ftpd_endpoint:9021/$file_name";
	return $ftp_link;
}
/**
 * Get list of dataset directories from filesystem
 *
 * @param $download_path path to the common drop area
 * @return array list of dataset directories
 */
function getDatasetDirectories(string $download_path): array
{
	$datasets = [] ;
	$handle = opendir($download_path);
	while (($file = readdir($handle)) !== false) {
		if ($file === '.' || $file === '..') {
			echo "skipping $file because dot file".PHP_EOL;
			continue;
		}
		if ( true != is_dir("$download_path/$file") ) {
			echo "skipping $file because not a directory".PHP_EOL;
			continue;
		}
		if (1 != preg_match("/\d+/",$file) ) {
			echo "skipping $file because not matching digits".PHP_EOL;
			continue;
		}
		array_push($datasets, $file);
	}
	return $datasets;
}

/**
 * Get list of files from filesystem
 *
 * @param string $path directory to scan
 * @return array list of files
 */
function getFiles(string $path): array
{
	clearstatcache();
	$scanned_directory = array_diff(scandir($path), array('..', '.', '.DS_Store'));
	return $scanned_directory;
}

/**
 * update a file flag
 */
function touchFlag()
{
	touch(FLAG_PATH);
}

/**
 * get flag file
 *
 * @param int DOI
 * @return string path to flag file
 */
function getFlag(int $doi_suffix): string
{
	return FLAG_PATH."/".$doi_suffix;
}

/**
 * set flag file
 *
 * @param int DOI
 * @return bool whether touching file was successful or not
 */
function setFlag(int $doi_suffix): bool
{
	return touch(FLAG_PATH."/".$doi_suffix);
}

/**
 * Compare whether a directory is newer than 2nd file
 *
 * If reference file is absent, we assume directory is newer
 * If directory file is abasent, then directory is not newer
 *
 * @param string directory to check if newer than referenc file
 * @param string reference file
 * @return bool true or false whether the first file is newer than 2nd file.
 *
 */
function isDirectoryNewerThan(string $dirToCheck, string $referenceFile): bool
{
	clearstatcache();
	if (false == file_exists($referenceFile)) {
		return true;
	}
	if (false == file_exists($dirToCheck)) {
		return false;
	}

	$dirTime = filemtime("$dirToCheck/.");
	$refTime = filemtime($referenceFile);
	echo "    is_newer:".PHP_EOL;
	echo "      dir mtime: ".$dirTime.PHP_EOL;
	echo "      flag mtime: ".$refTime.PHP_EOL;
	if ($dirTime >= $refTime) {
		return true;
	}
	return false;
}
/**
 * Verify whether the directory is newer than the file flag
 *
 * @param string $directory_path directory to compare modification time
 * @return boolean true if the directory is newer than the file flag, false o/w
 */
function is_newer(string $directory_path): bool
{
	clearstatcache();
	if (false == file_exists(FLAG_PATH)) {
		return true;
	}
	$dir_stats = stat($directory_path);
	$flag_stats = stat(FLAG_PATH);
	echo "  is_newer:".PHP_EOL;
	echo "    dir mtime: ".$dir_stats[9].PHP_EOL;
	echo "    flag mtime: ".$flag_stats[9].PHP_EOL;
	if ($dir_stats[9] >= $flag_stats[9]) {
		return true;
	}
	return false;
}

/**
 * Gather file metadata
 *
 * @param string $file_name the name of the file
 * @param int $datasetid the dataset identifier associated to the file
 * @return array array of metadata key/value pair
 */
function fileMetadata(string $file_name, int $dataset): array
{
	$file_path = "/home/downloader/$dataset/$file_name";
	$file_stats = stat($file_path);
	$metadata = array(
					"file_name" => $file_name,
					"data_type" => null,
					"format" => null,
					"size" => $file_stats[7],
					"link" => null,
					"md5" => null,
					"extension" => pathinfo($file_path, PATHINFO_EXTENSION) ?? "",
					"description" => $file_name
				);

	$metadata["format"] = getFileFormatFromFile($file_name);
	$metadata["data_type"] = getApproximateDataTypeFromFile($file_name);
	$metadata["link"] = generateFTPLink($file_name,$dataset);
	return $metadata;
}


/**
 * Connect to the database
 *
 * @return object return a database connection handle
 */
function connectDB(): object
{
$appconfig = parse_ini_file("/var/appconfig.ini");

		$db_user = $appconfig["db_user"];
		$db_password = $appconfig["db_password"];
		$db_source = $appconfig["db_source"];

		$dbh = new PDO("pgsql:host=database;dbname=$db_source", "$db_user", "$db_password");
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); //PHP warnings for SQL errors
		return $dbh ;
}

/**
 * Updating all the file table rows for a dataset whose files are being uploaded
 *
 * using delete and insert approach
 *
 * @param object $dbh database handle
 * @param int $dataset_doi dataset identifier (DOI suffix)
 * @return int number of row updated
 */
function updateFileTable(object $dbh, int $dataset_doi, array $uploadedFilesMetadata): int
{
	$result = 0;

	$getDatasetID = "select id from dataset where identifier = ?" ;
	$delete = "delete from file where dataset_id= ? and status = 'uploading'";
	$insert = "insert into file(dataset_id,name,size,status,location,extension,description) values(:d , :n , :z , 'uploading', :l, :e, :s)";

	$get_statement = $dbh->prepare($getDatasetID);
	$get_statement->bindParam(1, $dataset_doi);
	$get_statement->execute();
	$dataset = $get_statement->fetch(PDO::FETCH_OBJ);

	$delete_statement = $dbh->prepare($delete);
	$delete_statement->bindParam(1, $dataset->id);
	$delete_statement->execute();

	$insert_statement = $dbh->prepare($insert);
	$insert_statement->bindParam(':d', $dataset->id);
	$insert_statement->bindParam(':n', $name);
	$insert_statement->bindParam(':z', $size);
	$insert_statement->bindParam(':l', $location);
	// $insert_statement->bindParam(':f', $format);
	// $insert_statement->bindParam(':t', $data_type);
	$insert_statement->bindParam(':e', $extension);
	$insert_statement->bindParam(':s', $summary);
	foreach ($uploadedFilesMetadata as $file) {
		$name = $file["file_name"] ;
		$size = $file["size"] ;
		$location = $file["link"] ;
		// $format = $file["format"] ;
		// $data_type = $file["data_type"] ;
		$extension = $file["extension"] ;
		$summary = $file["description"] ;
		$result += $insert_statement->execute();
	}

	return $result;
}

clearstatcache();
$dbh = connectDB();
echo "Scanning file system...".PHP_EOL;

foreach (getDatasetDirectories("/home/downloader/") as $dataset_dir) {
	echo "  * Found dataset directory $dataset_dir".PHP_EOL;
	if ( isDirectoryNewerThan( "/home/downloader/$dataset_dir", getFlag($dataset_dir) ) ) {

		$files = [];
		foreach ( getFiles("/home/downloader/$dataset_dir") as $file ) {
			echo "    Gathering metadata for $file".PHP_EOL;
			array_push( $files, 	fileMetadata($file, $dataset_dir) );
		}
		echo "    Updating File table".PHP_EOL;
		$nbRecords = updateFileTable($dbh, $dataset_dir, $files);
		echo "    Number of records changed for files in $dataset_dir: $nbRecords".PHP_EOL;

		setFlag($dataset_dir);
	}
}


?>