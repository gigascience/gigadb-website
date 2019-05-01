<?php

	$thisurl = parse_url($_SERVER['REQUEST_URI']);
	parse_str($thisurl["query"], $params);
/*
  1. create folder in incoming
  2. create folder in repo
  3. generate random password
  4. create download password file
  5. create uploade password file
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
	$password_directory = "/var/private";
	mkdir("$in_directory/$dataset", 0700);
	mkdir("$out_directory/$dataset", 0700);
	mkdir("$password_directory/$dataset", 0700);
	clearstatcache();
	return file_exists("$in_directory/$dataset")
			&& file_exists("$out_directory/$dataset")
			&& file_exists("$password_directory/$dataset");
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
 * Create password files for upload and download accounts
 *
 * @param string $dataset DOI suffix
 * @param string $fileName file name for the password file
 * @return bool if file exists return true, false otherwise
 */
function makePasswordFile(string $dataset, string $fileName): bool
{
	$basepath = "/var/private";
	$password = generateString(20);

	$fileHandle = fopen("$basepath/$dataset/$fileName", "w");
	fwrite($fileHandle, $password);
	fwrite($fileHandle, "\n");
	fwrite($fileHandle, $password);
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
	exec("/var/scripts/create_upload_ftp.sh $dataset",$output, $status);
	error_log(implode("\n",$output));
	exec("/var/scripts/create_download_ftp.sh $dataset",$output, $status);
	error_log(implode("\n",$output));
    sleep(2);
    return !$status;
}

$result = true ;
$result = $result && makeDatasetDirectories($params['d']);
$result = $result && makePasswordFile($params['d'], "download_password.txt");
$esult = $result && makePasswordFile($params['d'], "upload_password.txt");
$esult = $result && createFTPAccount($params['d']);

?>
<!DOCTYPE html>
<html>
<head>
	<title>Prototype of File Uploade Wizard (Create drop box)</title>
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