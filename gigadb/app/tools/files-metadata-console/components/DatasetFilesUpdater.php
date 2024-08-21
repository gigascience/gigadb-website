<?php

declare(strict_types=1);

namespace app\components;

use Exception;
use GigaDB\models\Dataset;
use GigaDB\models\File;
use yii\base\Component;
use yii\helpers\Console;

/**
 * DatasetFilesUpdater
 *
 * encapsulate business logic for updating the file table
 */
final class DatasetFilesUpdater extends Component
{
    /**
     * @var string Dataset identifier for the dataset whose files need to be operated on
     */
    public string $doi;

    /** @const string  GIGADB_METADATA_DIR Path in bastion server where doi.filesizes can be found */
    const GIGADB_METADATA_DIR = '/var/share/gigadb/metadata/';

    /**
     * Update MD5 checksum attribute for all files in a dataset given its DOI
     *
     * @return int
     * @throws Exception
     */
    public function updateMD5FileAttributes(): int {
        $success = 0;
        // Dataset id is required for querying files
        $dataset = Dataset::find()->where(['identifier' => $this->doi])->one();
        if(is_null($dataset)) {
            throw new Exception("No dataset found in database with DOI $this->doi");
        }
    
        # Fetch and parse dataset md5 file
        $md5FilePath = DatasetFilesUpdater::GIGADB_METADATA_DIR . $this->doi . '.md5';
        if(!file_exists($md5FilePath)) {
            throw new Exception("$md5FilePath not found");
        }

        $contents = file_get_contents($md5FilePath);
        $lines = explode("\n", $contents);
        foreach ($lines as $line) {
            # Last line in $doi.md5 file might be empty
            if(!str_contains($line, '  ')) {
                break;
            }
            # md5 value and file name is separated by 2 spaces in doi.md5 file
            $tokens = explode("  ", $line);
            // Only parse lines with content in md5 file
            if($tokens[0] !== "") {
                $md5_value = $tokens[0];
                # Make use of whole file path
                $filepath = ltrim($tokens[1], './');
                if ($filepath === "$this->doi.md5")  // Ignore $doi.md5 file
                    continue;

                # Find file to be updated
                $file = File::find()
                    ->where(['dataset_id' => $dataset->id])
                    # Use % wildcard to ensure location ends with filename and
                    # another file with same filename in different directory is not
                    # accidentally updated
                    ->where("location LIKE :substr", array(':substr' => "%$filepath"))
                    ->one();
                if(!$file) {
                    echo("$filepath in $this->doi.md5 was not found in database" . PHP_EOL);
                }
                else {
                    $file->updateMd5Checksum($md5_value);
                    $success++;
                }
            }
        }
        return $success;
    }

    /**
     * Updates sizes for all files listed in doi.filesizes file located in 
     * gigadb-datasets-metadata S3 bucket.
     *
     * @return int returns the number of files that has been successfully updated
     * @throws Exception
     */
    public function updateFileSizes(): int
    {
        $success = 0;
        $d = Dataset::find()->where(['identifier' => $this->doi])->one();
        if(is_null($d)) {
            throw new Exception("No dataset found in database with DOI $this->doi");
        }

        $filesizesPath = DatasetFilesUpdater::GIGADB_METADATA_DIR . $this->doi . '.filesizes';
        if(!file_exists($filesizesPath)) {
            throw new Exception("$filesizesPath not found" . PHP_EOL);
        }

        $content = file_get_contents($filesizesPath);
        $lines = explode("\n", $content);
        foreach($lines as $line) {
            # Last line in .filesizes file might be empty
            if(!str_contains($line, "\t")) {
                break;
            }
            $tokens = explode("\t", $line);
            $size = (int)$tokens[0];
            $filepath = ltrim($tokens[1], './');
            # Find file to be updated
            $file = File::find()
                ->where(['dataset_id' => $d->id])
                # Use % wildcard to ensure location ends with filename and
                # another file with same filename in different directory is not
                # accidentally updated
                ->where("location LIKE :substr", array(':substr' => "%$filepath"))
                ->one();
            if($file) {
                # Update file size
                $file->size = $size;
                if ($file->save()) {
                    $success++;
                }
            }
            else {
                # Let user know when a file cannot be found but continue
                # processing remainder of doi.filesizes file
                echo("$filepath in $this->doi.filesizes was not found in database" . PHP_EOL);
            }
        }
        return $success;
    }
}
