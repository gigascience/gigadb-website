<?php
    require 'lib/db.php';

    $ftp_hostname = "fuw.rija.dev";
    $ftp_port = 9021;

    /**
     * the account class
     *  id | doi_suffix |  ulogin  |        utoken        |  dlogin  |        dtoken        | space_used | status |         created_at         |         updated_at         | retired_at
     */
    class Account {
        public $id;
        public $doi_suffix;
        public $ulogin;
        public $utoken;
        public $dlogin;
        public $dtoken;
        public $space_used;
        public $status;
        public $created_at;
        public $updated_at;
        public $retired_at;
    }

    /**
     * return array of accounts
     *
     * @param string $status filtering on status (active or retired)
     */
    function getAccounts(string $status): array
    {
        $dbh = connectDB();
        $sql = "select distinct * from account where status = ? order by created_at desc";
        $st = $dbh->prepare($sql);
        $st->bindParam(1, $status, PDO::PARAM_STR);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_CLASS, "Account");
    }

?>
<!DOCTYPE html>
<html>

<head>
    <title>Prototype of File Upload Wizard (Dashboard)</title>
</head>

<body>
    <h1>Prototype of File Upload Wizard</h1>
    <h2>Dashboard</h2>
    <h3>What this prototype does:</h3>
    <p>
        The table below lists for a fixed number of examples dataset DOIs:
        <ul>
         <li>a link to an uploader page for uploading the files of any size</li>
          <li>a link to the mockup page for downloading the files in context</li>
          <li>the ftp login/token to upload file using ftp instead of the uploader</li>
          <li>the ftp login/token to download the files using ftp instead of the mockup page</li>
          <li>the status of the dataset drop box account</li>
          <li>when the drop box account was created</li>
     </ul>
    </p>
    <h3>What the prototype is NOT meant to do:</h3>
    <p>
        <ul>
        <li>metadata capture</li>
        <li>sending email</li>
        <li>triggering and reacting on change of dataset status</li>
        <li>faithful and realistic layout</li>
        <li>faithful and realistic content</li>
        <li>managing the drop box account</li>
      </ul>
    </p>
    <h3>TODO:</h3>
    <p>
        <ul>
        <li>synchronising uploaded file with public ftp server</li>
        <li>monitoring</li>
        <li>deploy on Alibaba Cloud (currently blocked by lack of payment means accepted by them)</li>
      </ul>
  </p>
<!--     <ul>
    	<li> Current host: Local [yes], AWS [no], Digital Ocean [no], Alibaba Cloud [no]</li>
    	<li> Available disk space: %100 (of 20 GB)</li>
    	<li> Datasets last reset on: date</li>
    	<li> Datasets next reset on: date</li>
    </ul> -->
    <form>
        <table border="1">
        	<tr>
        		<th>Dataset</th>
        		<th>Go to Uploader page</th>
        		<th>Go to Mockup page</th>
        		<th>ftp upload user/token</th>
        		<th>ftp download user/token</th>
                <th>account status</th>
                <th>account creation date</th>
        	</tr>
            <?php
                foreach (getAccounts("active") as $account) {
            ?>
                <tr>
                    <td><?= $account->doi_suffix?></td>
                    <td><a id="Upload_<?= $account->doi_suffix?>" type="button" href="/uploader.php?d=<?= $account->doi_suffix?>">Uploader</a></td>
                    <td><a id="Upload_<?= $account->doi_suffix?>" type="button" href="/downloader.php?d=<?= $account->doi_suffix?>">Mockup</a></td>
                    <td><?= $account->ulogin . "/" . $account->utoken?></td>
                    <td><?= $account->dlogin . "/" . $account->dtoken?></td>
                    <td><?= $account->status ?></td>
                    <td><?= $account->created_at ?></td>
                </tr>
            <?php
                }
            ?>
        </table>
    </form>
    <hr>
    <div id="info_area">
        <h2>Alternative method to upload files, using FTP</h2>
        You will need to connect to the ftp server <b><?=  $ftp_hostname ?></b> on port <b><?= $ftp_port ?></b>
        using the username and token shown in the table for a given dataset.
        <pre>e.g:
$ ncftpput -u user -P <?= $ftp_port ?> -p token <?=  $ftp_hostname ?> / some_local_file
some_local_file:                         119.83 kB  159.64 kB/s
		</pre>
    </div>
</body>

</html>