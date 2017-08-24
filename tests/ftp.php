<?php
/**
 * Created by PhpStorm.
 * User: rija
 * Date: 16/02/2017
 * Time: 20:04
 */


$file = 'somefile.txt';
$remote_file = 'readme.txt';
$ftp_server = '10.1.1.33';
$ftp_user_name = 'anonymous';
$ftp_user_pass = 'anonymous@domain.com';

// set up basic connection
$conn_id = ftp_connect($ftp_server);

// login with username and password
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

// turn passive mode on
ftp_pasv($conn_id, true);

// change directory
ftp_chdir($conn_id, '/10.5524/100001_101000/100117/AltSplicing');

// return current directory
$current = ftp_pwd($conn_id);
echo $current;

//directory listing
$listing = ftp_rawlist($conn_id,'.');
var_dump($listing);
foreach ($listing as $row) {
    print_r($row);
}

// close the connection
ftp_close($conn_id);
?>