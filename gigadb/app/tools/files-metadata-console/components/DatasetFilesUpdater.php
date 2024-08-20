<?php

declare(strict_types=1);

namespace app\components;

use Exception;
use GigaDB\models\Dataset;
use GigaDB\models\File;
use GigaDB\services\URLsService;
use yii\base\Component;

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

        $filesizesPath = DatasetFilesUpdater::GIGADB_METADATA_DIR . $this->doi . '.filesizes';
        if(!file_exists($filesizesPath)) {
            throw new Exception("$filesizesPath not found");
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
                throw new Exception("$filepath in $this->doi.filesizes was not found in database" . PHP_EOL);
            }
        }
        return $success;
    }
}
