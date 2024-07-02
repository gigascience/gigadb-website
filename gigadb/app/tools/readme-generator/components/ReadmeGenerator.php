<?php

namespace app\components;

use GigaDB\models\FileAttributes;
use GigaDB\models\File;
use Exception;
use GigaDB\models\Dataset;
use yii\base\Component;
use yii\base\UserException;

/**
 * Component service to output contents for a readme file for a dataset
 */
class ReadmeGenerator extends Component
{
    // Character width of text in readme file.
    public const STRING_WIDTH = 80;


    /**
     * Initialize component
     */
    public function init()
    {
        parent::init();
    }


    /**
     * Returns the contents for a dataset's readme file
     *
     * @param string $doi DOI for a dataset.
     *
     * @return string Contents of readme file
     * @throws UserException Dataset DOI not found.
     */
    public function getReadme(string $doi): string
    {
        // Check dataset exists otherwise throw exception to exit.
        $dataset = Dataset::findOne(['identifier' => $doi]);
        if (is_null($dataset)) {
            throw new UserException('Dataset ' . $doi . ' not found');
        }
        // Use array to store readme information.
        $formattedTitle = wordwrap('[Title] ' . $dataset->title, self::STRING_WIDTH, PHP_EOL);
        $readme         = [
            '[DOI] 10.5524/' . $doi . PHP_EOL,
            $formattedTitle . PHP_EOL,
            '[Release Date] ' . $dataset->publication_date . PHP_EOL,
        ];

        $citation        = '[Citation] ';
        $authors         = $dataset->authors;
        $numberOfAuthors = count($authors);
        for ($i = 0; $i < $numberOfAuthors; $i++) {
            $firstNameInitial  = substr($authors[$i]->first_name, 0, 1);
            $middleNameInitial = substr($authors[$i]->middle_name, 0, 1);
            $surname           = $authors[$i]->surname;
            $fullName          = $surname . ', ' . $firstNameInitial . $middleNameInitial;
            $lastIndex         = (count($authors) - 1);
            if ($i === $lastIndex) {
                $citation .= $fullName . ' ';
            } else {
                $citation .= $fullName . '; ';
            }
        }

        $publicationYear   = substr($dataset->publication_date, 0, 4);
        $citation         .= '(' . $publicationYear . '): ';
        $citation         .= $dataset->title . ' GigaScience Database. https://dx.doi.org/10.5524/' . $doi . PHP_EOL;
        $formattedCitation = wordwrap($citation, self::STRING_WIDTH, PHP_EOL);
        $readme[]          = $formattedCitation;

        $datasetType = '[Data Type] ';
        // Returns array of DatasetType objects.
        $datasetTypes         = $dataset->datasetTypes;
        $numberOfDatasetTypes = count($datasetTypes);
        for ($i = 0; $i < $numberOfDatasetTypes; $i++) {
            // $type is an ActiveQuery object
            $type      = $datasetTypes[$i]->getType();
            $typeName  = $type->one()->name;
            $lastIndex = (count($datasetTypes) - 1);
            if ($i === $lastIndex) {
                $datasetType .= $typeName . PHP_EOL;
            } else {
                $datasetType .= $typeName . ',';
            }
        }

        $readme[] = $datasetType;

        $formattedDescription = wordwrap('[Dataset Summary] ' . $dataset->description, self::STRING_WIDTH, PHP_EOL);
        $readme[]             = $formattedDescription . PHP_EOL;

        $readme[] = '[File Location] ' . $dataset->ftp_site . PHP_EOL;

        $fileNameDescription = '[File name] - [File Description]' . PHP_EOL;
        // Returns array of File objects.
        $files = $dataset->files;
        foreach ($files as $file) {
            $fileName             = $file->name;
            $fileDescription      = $file->description;
            $fileLocation         = $file->location;
            $fileNameDescription .= $fileName . '  -  ' . $fileDescription . ' - ' . $fileLocation . PHP_EOL;
        }

        $readme[] = $fileNameDescription;

        $license          = 'All files and data are distributed under the CC0 1.0 Universal (CC0 1.0) Public ';
        $license         .= 'Domain Dedication (https://creativecommons.org/publicdomain/zero/1.0/), unless ';
        $license         .= 'specifically stated otherwise, see http://gigadb.org/site/term for more details.';
        $formattedLicense = wordwrap($license, self::STRING_WIDTH, PHP_EOL);
        $readme[]         = '[License]' . PHP_EOL . $formattedLicense . PHP_EOL;

        $readme[] = '[Comments]' . PHP_EOL;

        $readme[] = '[End]' . PHP_EOL;

        // Convert readme array to string.
        return implode(PHP_EOL, $readme);
    }

    /**
     * Update an exiting readme entry in file table and file_attributes table
     * or
     * Create an entry file table and file_attributes table
     *
     * @param string $doi DOI for a dataset.
     * @param string $filename Readme filename in readme_$doi.txt
     * @param int $fileSize File size of the readme file
     * @param string $md5 md5 value of the readme file
     * @param string $bucketPath
     * @return void
     * @throws UserException Dataset DOI not found.
     */
    public function updateOrCreate($doi, $filename, $fileSize, $md5, $bucketPath)
    {
        $dataset = Dataset::findOne(['identifier' => $doi]);
        if (is_null($dataset)) {
            throw new UserException('Dataset ' . $doi . ' not found');
        }

        $fileEntry = File::findOne(['dataset_id' => $dataset->id, 'name' => $filename]);
        if (!$fileEntry) {
            $fileEntry = File::findOne(['dataset_id' => $dataset->id, 'name' => 'readme.txt']);
        }

        if (!$fileEntry) {
            $fileEntry = new File();
            $fileEntry->dataset_id = $dataset->id;
        }

        if ($doi <= 101000) {
            $dir_range = "100001_101000";
        } elseif ($doi <= 102000 && $doi >= 101001) {
            $dir_range = "101001_102000";
        } elseif ($doi <= 103000 && $doi >= 102001) {
            $dir_range = "102001_103000";
        }

        $bucketPath = str_replace('wasabi:', '', $bucketPath);
        $location = "https://s3.ap-northeast-1.wasabisys.com/" . "$bucketPath/$dir_range/$doi/$filename";

        $fileEntry->name = $filename;
        $fileEntry->size = $fileSize;
        $fileEntry->location = $location;
        $fileEntry->extension = "txt";
        $fileEntry->save();

        // Attribute id for MD5 checksum
        $attributeId = 605;

        $fa = FileAttributes::findOne(['file_id' => $fileEntry->id]);

        if (!$fa) {
            $fa = new FileAttributes();
            $fa->file_id = $fileEntry->id;
            $fa->attribute_id = $attributeId;
        }

        $fa->value = $md5;
        $fa->save();
    }
}
