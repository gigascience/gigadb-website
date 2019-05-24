<?php

	require 'lib/db.php';

    $appconfig = parse_ini_file("/var/appconfig.ini");
    $web_endpoint = $appconfig["web_endpoint"];


	$thisurl = parse_url($_SERVER['REQUEST_URI']);
	parse_str($thisurl["query"], $params);
/*
  1. create folder in incoming
  2. create folder in repo
  3. generate random token
  4. create download token file
  5. create uploade token file
*/

/**
 * Create folder in the right place
 *
 * @param string $dataset DOI suffix
 * @return bool true if all diretories are created, false otherwise
 */
function makeDatasetDirectories(string $dataset): bool
{
	$in_directory = "/var/incoming/ftp";
	$out_directory = "/var/repo";
	$token_directory = "/var/private";
	mkdir("$in_directory/$dataset", 0770);
	mkdir("$out_directory/$dataset", 0755);
	mkdir("$token_directory/$dataset", 0750);
	clearstatcache();
	return file_exists("$in_directory/$dataset")
			&& file_exists("$out_directory/$dataset")
			&& file_exists("$token_directory/$dataset");
}

/**
 * Generate random string
 *
 * @param int $strength strength of random string
 * @return string random string
 */
function generateString(int $strength = 16): string {
	$range = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $input_length = strlen($range);
    $random_string = '';
    for($i = 0; $i < $strength; $i++) {
        $random_character = $range[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }

    return $random_string;
}

/**
 * Create token files for upload and download accounts
 *
 * @param string $dataset DOI suffix
 * @param string $fileName file name for the token file
 * @return bool if file exists return true, false otherwise
 */
function makeTokenFile(string $dataset, string $fileName): bool
{
	$basepath = "/var/private";
	$token = generateString(20);

	$fileHandle = fopen("$basepath/$dataset/$fileName", "w");
	fwrite($fileHandle, $token);
	fwrite($fileHandle, "\n");
	fwrite($fileHandle, $token);
	fclose($fileHandle);
	clearstatcache();
	return file_exists("$basepath/$dataset/$fileName");

}

/**
 * Create ftp account on the ftpd container using Docker API
 *
 * @param string $dataset DOI suffix
 * @return bool if successful return true, otherwise false
 */
function createFTPAccount(string $dataset): bool
{
	$status = 0 ;
	exec("/var/scripts/create_upload_ftp.sh $dataset",$output1, $status);
	error_log(implode("\n",$output1));
	exec("/var/scripts/create_download_ftp.sh $dataset",$output2, $status);
	error_log(implode("\n",$output2));
    sleep(2);
    return !$status;
}

/**
 * After creating folder and ftp accounts, add an entry in the account table
 *
 * @param string $dataset DOI suffix
 * @param string $downloadTokenFile file containing the download token
 * @param string $uploadTokenFile file containing the upload token
 * @return bool whether the account record was succesfully created or not
 */
function createAccountRecord(string $dataset,
							string $downloadTokenFile,
							string $uploadTokenFile
							): bool
{
	$dbh = connectDB();
	$result = 0;

	$uploadLogin = "u-$dataset";
	$downloadLogin = "d-$dataset";
	$uploadToken = file("/var/private/$dataset/$uploadTokenFile")[0];
	$downloadToken = file("/var/private/$dataset/$downloadTokenFile")[0];
	$status = "active";

	$insert = "insert into account(doi_suffix,ulogin,utoken,dlogin,dtoken,status) select :d , :ul , :up , :dl, :dp, :s where not exists (select * from account where doi_suffix = :d and status = 'active')";
	$insert_statement = $dbh->prepare($insert);
	$insert_statement->bindParam(':d', $dataset);
	$insert_statement->bindParam(':ul', $uploadLogin);
	$insert_statement->bindParam(':up', $uploadToken);
	$insert_statement->bindParam(':dl', $downloadLogin);
	$insert_statement->bindParam(':dp', $downloadToken);
	$insert_statement->bindParam(':s', $status);

	$result = $insert_statement->execute();


	return 1 == $result;
}

$uTokenFile = "upload_token.txt" ;
$dTokenFile = "download_token.txt" ;
$result = true ;
$result = $result && makeDatasetDirectories($params['d']);
$result = $result && makeTokenFile($params['d'], $dTokenFile);
$result = $result && makeTokenFile($params['d'], $uTokenFile);
$result = $result && createFTPAccount($params['d']);
$result = $result && createAccountRecord($params['d'], $dTokenFile, $uTokenFile);

?>
<!DOCTYPE html>
<html>
<head>
	<title>Prototype of File Uploade Wizard (Create drop box)</title>
</head>
<body>
	<nav><a href="<?= $web_endpoint ?>">[Go back to Dashboard]</a></nav>
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
