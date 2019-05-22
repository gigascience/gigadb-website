#!/usr/bin/php

<?php

/**
 * Insure group writability for list of dataset directories
 *
 * @param $path path to the common drop area
 */
function checkPermissions(string $path)
{
	$handle = opendir($path);
	while (($file = readdir($handle)) !== false) {
		echo "file: " .$file.PHP_EOL;
		if ($file === '.' || $file === '..') {
			echo "is a dot file".PHP_EOL;
			continue;
		}
		if ( true != is_dir("$path/$file") ) {
			echo "is not a directory".PHP_EOL;
			continue;
		}
		if (1 != preg_match("/\d+/",$file) ) {
			echo "doesn't match /\d+/ pattern".PHP_EOL;
			continue;
		}

		$perms = substr(sprintf('%o', fileperms("$path/$file")), -4);
		echo "permissions: $perms".PHP_EOL;
		if ( "0770" !== $perms ) {
			echo "Enabling group write to: " .$file.PHP_EOL;
			chmod("$path/$file", 0770);
			clearstatcache();
			$newperms = substr(sprintf('%o', fileperms("$path/$file")), -4);
			echo "permissions after change: $newperms".PHP_EOL;
		}

	}
}

/**
 * move files uploaded by ftp from one directory to another
 *
 * @param string $source_path path of directory whose files to move
 * @param string $destination_path path of directory where to move the files
 * @return bool whether the copy was successful or not
 */
function moveFilesFromDirToDir(string $source_path, string $target_path): bool
{
	echo "Move files from $source_path/ to $target_path...".PHP_EOL;
	exec("cp -rp $source_path/* $target_path/", $output, $status);
	error_log(implode("\n",$output));
	return !$status;
}

/**
 * add file metadata to database
 * @return bool whether the process was successful or not
 *
 */
function recordFilesMetadata(): bool
{
	echo "Save metadata in the database...".PHP_EOL;
	exec("/commands/file-to-db.php", $output, $status);
	error_log(implode("\n",$output));
	return !$status;
}

checkPermissions("/home/uploader");

$status = true;
$status = $status && moveFilesFromDirToDir("/home/uploader","/home/downloader");

$status = $status && recordFilesMetadata();

exit(!$status);

?>