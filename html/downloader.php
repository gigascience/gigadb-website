<?php

	require 'lib/db.php';

	$thisurl = parse_url($_SERVER['REQUEST_URI']);
	parse_str($thisurl["query"], $params);
	// echo $params["d"];

	/**
	 * the file class
	 * id | doi_suffix | name | size | status | location | description | initial_md5 | format | data_type | created_at | updated_at
	 */
	class File {
	    public $id;
	    public $doi_suffix;
	    public $name;
	    public $size;
	    public $status;
	    public $location;
	    public $description;
	    public $initial_md5;
	    public $format;
	    public $data_type;
	    public $created_at;
	    public $updated_at;
	}

	/**
	 * Return the list of file and metadata for a given dataset
	 *
	 * @param object $dbh database handle
	 * @param int $dataset DOI suffix
	 * @return array list of files
	 */
	function getFileTable(object $dbh, int $dataset): array
	{
		$sql = "select * from file where doi_suffix= ? and status = 'uploading'";
		$st = $dbh->prepare($sql);
		$st->bindParam(1, $dataset);
		$st->execute();
		return $st->fetchAll(PDO::FETCH_CLASS, "File");
	}

	$dbh = connectDB();
	$table =  getFileTable($dbh, $params["d"]);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Prototype for File Upload Wizard (Mockup)</title>
</head>

<body>
    <headers>
        <h1>Mockup for dataset
            <?= $params["d"]?>
        </h1>
    </headers>
    <main role="main">
        <section>
            <article>
                <img style="float:left;padding:0.5em" src="https://picsum.photos/id/1002/300/200">
                <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula
                eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient
                montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque
                eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo,
                fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut,
                imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.
                Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate
                eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac,
                enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus
                viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam<p>

                <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula
                eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient
                montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque
                eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo,
                fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut,
                imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.
                Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate
                eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac,
                enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus
                viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam<p>
            </article>
        </section>
        <section>
            <h2>Files</h2>
            <table border="1">
                <tr>
                    <th>id</th>
                    <th>doi_suffix</th>
                    <th>name</th>
                    <th>size</th>
                    <!-- <th>status</th> -->
                    <th>location</th>
                    <!-- <th>description</th> -->
                    <!-- <th>initial_md5</th> -->
                    <!-- <th>format</th> -->
                    <!-- <th>data_type</th> -->
                    <th>created_at</th>
                    <th>updated_at</th>
                </tr>
                <? foreach ($table as $file) { ?>
                <tr>
                    <td>
                        <?= $file->id ?>
                    </td>
                    <td>
                        <?= $file->doi_suffix ?>
                    </td>
                    <td>
                        <?= $file->name ?>
                    </td>
                    <td>
                        <?= $file->size ?>
                    </td>
<!--                     <td>
                        <?= $file->status ?>
                    </td> -->
                    <td><a href="<?= $file->location ?>">
                            <?= $file->location ?></a></td>
<!--                     <td>
                        <?= $file->description ?>
                    </td>
                    <td>
                        <?= $file->initial_md5 ?>
                    </td>
                    <td>
                        <?= $file->format ?>
                    </td>
                    <td>
                        <?= $file->data_type ?>
                    </td> -->
                    <td>
                        <?= $file->created_at ?>
                    </td>
                    <td>
                        <?= $file->updated_at ?>
                    </td>
                </tr>
                <? } ?>
            </table>
        </section>
    </main>
    <footer>
        <em>(c) 2019, Prototype of mockup page for GigaDB</em>
    </footer>
</body>

</html>