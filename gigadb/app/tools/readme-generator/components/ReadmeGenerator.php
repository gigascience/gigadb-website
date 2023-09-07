<?php

namespace app\components;

use Exception;
use GigaDB\models\Dataset;
use yii\base\Component;

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
     * @throws Exception Dataset DOI not found.
     */
    public function getReadme(string $doi): string
    {
        // Check dataset exists otherwise throw exception to exit.
        $dataset = Dataset::findOne(['identifier' => $doi]);
        if (is_null($dataset)) {
            throw new Exception('Dataset ' . $doi . ' not found');
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

        $formattedDescription = wordwrap('[Data Summary] ' . $dataset->description, self::STRING_WIDTH, PHP_EOL);
        $readme[]             = $formattedDescription . PHP_EOL;

        $readme[] = '[File Location] ' . $dataset->ftp_site . PHP_EOL;

        $fileNameDescription = '[File name] - [File Description]' . PHP_EOL;
        // Returns array of File objects.
        $files = $dataset->files;
        foreach ($files as $file) {
            $fileName             = $file->name;
            $fileDescription      = $file->description;
            $fileNameDescription .= $fileName . '  -  ' . $fileDescription . PHP_EOL;
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
}
