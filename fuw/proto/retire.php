<?php

    require 'lib/db.php';

    $appconfig = parse_ini_file("/var/appconfig.ini");
    $web_endpoint = $appconfig["web_endpoint"];
    $api_endpoint = $appconfig["api_endpoint"];
    $jwt_token = $appconfig["dummy_jwt_token"];


	$thisurl = parse_url($_SERVER['REQUEST_URI']);
	parse_str($thisurl["query"], $params);

	// retrieve id of account:
	$dbh = connectDB();
    $sql = "select id from filedrop_account where status='active' and doi = ? ";
    $st = $dbh->prepare($sql);
    $st->bindParam(1, $params['d'], PDO::PARAM_STR);
    $st->execute();
    $result = $st->fetch(PDO::FETCH_ASSOC);
    $account_id = $result['id'];

	// pass the JWT token
	$headers = [ "Authorization: Bearer $jwt_token"];

	//
	// A very simple PHP example that sends a HTTP POST to a remote site
	//

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $api_endpoint."/$account_id");
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	// In real life you should use something like:
	// curl_setopt($ch, CURLOPT_POSTFIELDS, 
	//          http_build_query(array('postvar1' => 'value1')));

	// Receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$server_output = curl_exec($ch);
	$server_errno = curl_errno($ch);

	if($server_errno)
	{
	    $server_error = curl_error($ch);
	}
	curl_close ($ch);

?>
<!DOCTYPE html>
<html>
<head>
	<title>Prototype of File Uploade Wizard (Terminate Filedrop account)</title>
</head>
<body>
	<nav><a href="<?= $web_endpoint ?>">[Go back to Dashboard]</a></nav>
	<?
		if (false === $server_output) {
			echo "<p><b>Failed<b></p>";
		}
		else {
			echo "<p><b>Success<b></p>";
		}
	 ?>
</body>
</html>