<?php

	require 'lib/db.php';

	$thisurl = parse_url($_SERVER['REQUEST_URI']);
	parse_str($thisurl["query"], $params);
/*
  1. recursively delete folder in incoming
  2. recursively delete folder in repo
  3. remove download token file and parent folder
  4. remove uploader token file and parent folder
  5. remove FTP accounts
  6. set the account record to "retired"
*/


/**
 * recursively remove directories
 *
 * @param $dir directory to delete recursively
 * @return bool true on success otherwise false
 */
function delTree(string $dir): bool {
   $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
      (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
  } 

/**
 * Remove folder in the right place
 *
 * @param string $dataset DOI suffix
 * @return bool true if all diretories are created, false otherwise
 */
function removeDatasetDirectories(string $dataset): bool
{
	$in_directory = "/var/incoming/ftp";
	$out_directory = "/var/repo";
	$token_directory = "/var/private";
	delTree("$in_directory/$dataset");
	delTree("$out_directory/$dataset");
	delTree("$token_directory/$dataset");
	clearstatcache();
	return !file_exists("$in_directory/$dataset")
			&& !file_exists("$out_directory/$dataset")
			&& !file_exists("$token_directory/$dataset");
}



/**
 * remove ftp account on the ftpd container using Docker API
 *
 * @param string $dataset DOI suffix
 * @return bool if successful return true, otherwise false
 */
function removeFTPAccount(string $dataset): bool
{
	$status = 0 ;
	exec("/var/scripts/remove_upload_ftp.sh $dataset",$output1, $status);
	error_log(implode("\n",$output1));
	exec("/var/scripts/remove_download_ftp.sh $dataset",$output2, $status);
	error_log(implode("\n",$output2));
    sleep(2);
    return !$status;
}

/**
 * Delete entry in file table
 *
 * @param string $string dataset
 * @return bool whether the deletion was successful or not
 */
function deleteFileRecords(string $dataset): bool
{
	$dbh = connectDB();
	$delete = "delete from file where doi_suffix= ? and status = 'uploading'";
	$delete_statement = $dbh->prepare($delete);
	$delete_statement->bindParam(1, $dataset);
	return $delete_statement->execute();
}

/**
 * update account record as "retired"
 *
 * @param string $dataset DOI suffix
 * @param string $status status to update the record with with
 * @return bool whether the account record was succesfully updated or not
 */
function updateAccountRecord(string $dataset, string $status): bool
{
	$dbh = connectDB();
	$result = 0;

	$sql = "update account set status=? where doi_suffix = ?";
	$st = $dbh->prepare($sql);
	$st->bindParam(1, $status, PDO::PARAM_STR);
	$st->bindParam(2, $dataset);

	$result = $st->execute();

	return 1 == $result;
}


$result = true ;
$result = $result && removeDatasetDirectories($params['d']);
$esult = $result && removeFTPAccount($params['d']);
$esult = $result && updateAccountRecord($params['d'], "retired");
$esult = $result && deleteFileRecords($params['d']);

?>
<!DOCTYPE html>
<html>
<head>
	<title>Prototype of File Uploade Wizard (Retire drop box)</title>
</head>
<body>
	<?
		if (true == $result) {
			echo "<p><b>Success<b></p>";
		}
		else {
			echo "<p><b>Failed<b></p>";
		}
	 ?>
</body>
</html>