<?php

namespace app\components;

use Exception;
use yii\base\Component;
use app\models\Dataset;

/**
 * Component service to output contents for a readme file for a dataset
 *
 * @author Peter Li <peter+git@gigasciencejournal.com>
 * @license GPL-3.0
 */
class DatasetService extends Component
{
    // Character width of text in readme file
    const STRING_WIDTH = 80;

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
     * @param string $doi DOI for a dataset
     * @return string Contents of readme file
     * @throws Exception Dataset DOI not found
     */
    public function getReadme(string $doi): string
    {
        // Check dataset exists otherwise throw exception to exit
        $dataset = Dataset::findOne(['identifier' => $doi]);
        if (!$dataset) {
            throw new Exception("Dataset $doi not found");
        }

        // Use array to store readme information
        $formatted_title = wordwrap("[Title] $dataset->title", self::STRING_WIDTH, PHP_EOL);
        $readme = [ "[DOI] 10.5524/$doi".PHP_EOL,
            $formatted_title.PHP_EOL,
            "[Release Date] $dataset->publication_date".PHP_EOL
        ];

        // [Citation]
        $citation = "[Citation] ";
        $authors = $dataset->authors;
        for ($i = 0; $i < count($authors); $i++) {
            $first_name_initial = substr($authors[$i]->first_name, 0, 1);
            $middle_name_initial = substr($authors[$i]->middle_name, 0, 1);
            $surname = $authors[$i]->surname;
            $full_name = "$surname, $first_name_initial$middle_name_initial";
            $last_index = count($authors)-1;
            if ($i == $last_index) {
                $citation .= "$full_name ";
            } else {
                $citation .= "$full_name; ";
            }
        }

        $publication_year = substr($dataset->publication_date, 0, 4);
        $citation .= "($publication_year): ";
        $citation .= "$dataset->title GigaScience Database. https://dx.doi.org/10.5524/$doi".PHP_EOL;
        $formatted_citation = wordwrap("$citation", self::STRING_WIDTH, PHP_EOL);
        $readme[] = $formatted_citation;

        // [Data Type]
        $dataset_type = "[Data Type] ";
        // Returns array of DatasetType objects
        $datasetTypes = $dataset->datasetTypes;
        for ($i = 0; $i < count($datasetTypes); $i++) {
            // $type is an ActiveQuery object
            $type = $datasetTypes[$i]->getType();
            $typeName = $type->one()->name;
            $last_index = count($datasetTypes)-1;
            if ($i == $last_index) {
                $dataset_type .= $typeName . PHP_EOL;
            } else {
                $dataset_type .= "$typeName,";
            }
        }
        $readme[] = $dataset_type;

        // [Dataset Summary]
        $formatted_description = wordwrap("[Data Summary] $dataset->description", self::STRING_WIDTH, PHP_EOL);
        $readme[] = "$formatted_description".PHP_EOL;

        // [File Location]
        $readme[] = "[File Location] $dataset->ftp_site".PHP_EOL;

        // [File name] - [File Description]
        $file_name_description = "[File name] - [File Description]".PHP_EOL;
        // Returns array of File objects
        $files = $dataset->files;
        foreach ($files as $file) {
            $file_name = $file->name;
            $file_description = $file->description;
            $file_name_description .= "$file_name  -  $file_description".PHP_EOL;
        }
        $readme[] = $file_name_description;

        // [License]
        $license = <<<LIC
        All files and data are distributed under the Creative Commons Attribution-CC0 
        License unless specifically stated otherwise, see http://gigadb.org/site/term 
        for more details.
        LIC;
        $formatted_license = wordwrap($license, self::STRING_WIDTH, PHP_EOL);
        $readme[] = "[License]".PHP_EOL.$formatted_license.PHP_EOL;

        // [Comments]
        $readme[] = "[Comments]".PHP_EOL;

        //[End]
        $readme[] = "[End]".PHP_EOL;

        // Convert readme array to string
        return implode(PHP_EOL, $readme);
    }
}
?>
