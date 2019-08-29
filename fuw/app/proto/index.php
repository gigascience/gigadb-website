<?php
    require 'lib/db.php';

    $appconfig = parse_ini_file("/var/appconfig.ini");
    $ftpd_endpoint = $appconfig["ftpd_endpoint"];
    $ftpd_port = $appconfig["ftpd_port"];
    $web_endpoint = $appconfig["web_endpoint"];

    $ftpd_port = 9021;

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
        $sql = "select distinct * from account where status = ? order by doi_suffix";
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
    <h2>Goal</h2>
    <ul>
        <li>To inform the detailed software architecture and interactions mechanisms</li>
        <li>To identify and design out fragile and underperforming mechanisms</li>
    </ul>
    <h2>Users Dashboard</h2>
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
                <th>ftp upload username/token</th>
                <th>ftp download username/token</th>
                <th>account status</th>
                <th>account creation date</th>
            </tr>
            <?php
                foreach (getAccounts("active") as $account) {
            ?>
            <tr>
                <td>
                    <?= $account->doi?>
                </td>
                <td><a id="Upload_<?= $account->doi?>" type="button" href="/proto/uploader.php?d=<?= $account->doi?>">Uploader</a></td>
                <td><a id="Upload_<?= $account->doi?>" type="button" href="/proto/downloader.php?d=<?= $account->doi?>">Mockup</a></td>
                <td>
                    <?= $account->upload_login . "/" . $account->upload_token?>
                </td>
                <td>
                    <?= $account->download_login . "/" . $account->download_token?>
                </td>
                <td>
                    <?= $account->status ?>
                </td>
                <td>
                    <?= $account->created_at ?>
                </td>
            </tr>
            <?php
                }
            ?>
        </table>
    </form>
    <h2>Management Dashboard</h2>
    <table border="2">
        <tr>
            <th>DOI</th>
            <th>Click to Create</th>
            <th>Click to Delete</th>
        </tr>
        <tr>
            <td>100004</td>
            <td><a href="/proto/create.php?d=100004">Create Drop Box Account</a></td>
            <td><a href="/proto/retire.php?d=100004">Delete Drop Box Account</a></td>
        </tr>
        <tr>
            <td>100005</td>
            <td><a href="/proto/create.php?d=100005">Create Drop Box Account</a></td>
            <td><a href="/proto/retire.php?d=100005">Delete Drop Box Account</a></td>
        </tr>
    </table>
    <hr>
    <div id="info_area">
        <h2>Alternative method to upload files, using FTP</h2>
        You will need to connect to the ftp server <b>
            <?=  $ftpd_endpoint ?></b> on port <b>
            <?= $ftpd_port ?></b>
        using as login/password the <em>username</em> and <em>token</em> shown in the Users Dashboard table for a given dataset.
        <pre>e.g:

$ ncftpput -u username -P <?= $ftpd_port ?> -p token <?=  $ftpd_endpoint ?> / some_local_file
some_local_file:                         119.83 kB  159.64 kB/s
        </pre>
    </div>
    <h2>Scope</h2>
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
    <h3>What the prototype won't do:</h3>
    <p>
        <ul>
            <li>sending emails</li>
            <li>triggering and reacting on change of dataset status</li>
            <li>faithful and realistic layout</li>
            <li>faithful and realistic content</li>
        </ul>
    </p>
    <h2>TODO:</h2>
    <p>
        <ul>
            <li>synchronising uploaded files with public ftp server</li>
            <li>monitoring</li>
            <li>alerting</li>
            <li>deploy on Alibaba Cloud (currently blocked by payment issues)</li>
            <li>basic metadata capture</li>
        </ul>
    </p>
</body>

</html>
