<?php


?>
<!DOCTYPE html>
<html>

<head>
    <title>Prototype of File Upload Wizard (Dashboard)</title>
</head>

<body>
    <h1>Prototype of File Upload Wizard</h1>
    <h2>Dashboard</h2>
    <ul>
    	<li> Current host: Local [yes], AWS [no], Digital Ocean [no], Alibaba Cloud [no]</li>
    	<li> Available disk space: %100 (of 20 GB)</li>
    	<li> Datasets last reset on: date</li>
    	<li> Datasets next reset on: date</li>
    </ul>
    <form>
        <table border="1">
        	<tr>
        		<th>Dataset</th>
        		<th>Go to Uploader page</th>
        		<th>Go to Mockup page</th>
        		<th>ftp upload user</th>
        		<th>ftp upload password</th>
        		<th>ftp port</th>
        	</tr>

            <tr>
                <td>100006</td>
                <td><a id="Upload_100006" type="button" href="/uploader.php?d=100006">Uploader</a></td>
                <td><a id="Upload_100006" type="button" href="/downloader.php?d=100006">Mockup</a></td>
                <td>u-100006</td>
                <td>TODO</td>
                <td>9021</td>
            </tr>
        </table>
    </form>
    <hr>
    <div id="info_area">
        <h2>How to upload using ftp</h2>
        Use the user, ftp port and password shown in dashboard.
        <pre>e.g:
ncftpput -u u-100001 -P 9021 -p  password_see_above localhost / some_local_file
		</pre>
        <h2>File listings of dataset diretory (FTP)</h2>
    </div>
</body>

</html>