<?php
    require 'lib/db.php';
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
        $sql = "select distinct * from account where status = ? order by created_at desc FETCH FIRST 1 ROWS ONLY";
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
        <h2>How to upload using ftp</h2>
        Use the user, ftp port and password shown in dashboard.
        <pre>e.g:
ncftpput -u u-100001 -P 9021 -p  password_see_above localhost / some_local_file
		</pre>
        <h2>File listings of dataset diretory (FTP)</h2>
    </div>
</body>

</html>
