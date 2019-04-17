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
	$handle = fopen("/var/access/download_password.txt", "r");
	$line = fgets($handle) ;
	if (true == $line) {
		$download_password = chop($line);
	}
	fclose($handle);

	$ftp_link = "ftp://d-$dataset:$download_password@localhost:9021/$file_name";
	return $ftp_link;
}
/**
 * Get list of datasetd directories from filesystem
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
			continue;
		}
		if ( true != is_dir("$download_path/$file") ) {
			continue;
		}
		if (1 != preg_match("/\d+/",$file) ) {
			continue;
		}
		if (true != is_newer("$download_path/$file") ) {
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
	return scandir($path);
}

/**
 * update a file flag
 */
function touchFlag()
{
	touch(FLAG_PATH);
}

/**
 * verify whether the directory is newer than the file flag
 * @param string $directory_path directory to compare modification time
 * @return boolean true if the directory is newer than the file flag, false o/w
 */
function is_newer(string $directory_path): bool
{

	if (false == file_exists(FLAG_PATH)) {
		return true;
	}
	$dir_stats = stat($directory_path);
	$flag_stats = stat(FLAG_PATH);
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
					"description" => null
				);

	$metadata["format"] = getFileFormatFromFile($file_name);
	$metadata["data_type"] = getApproximateDataTypeFromFile($file_name);
	$metadata["link"] = generateFTPLink($file_name,$dataset);
	return $metadata;
}

clearstatcache();
echo "Scanning file system".PHP_EOL;

foreach (getDatasetDirectories("/home/downloader/") as $dataset_dir) {
	echo "----------------- $dataset_dir ---------------".PHP_EOL;
	foreach ( getFiles("/home/downloader/$dataset_dir") as $file ) {
		echo "$file metadata:".PHP_EOL;
		var_dump(fileMetadata($file, $dataset_dir));
	}
}

touchFlag();

?>