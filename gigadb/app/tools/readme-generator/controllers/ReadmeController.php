<?php

namespace app\controllers;

use \Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use \yii\helpers\Console;
use \yii\console\Controller;
use \yii\console\ExitCode;
use app\models\Dataset;

/**
 * The tool for generating dataset readme files
 * @package app\commands
 */
class ReadmeController extends Controller
{
    /**
     * @var string $doi The DOI of the dataset that the readme file should be created for
     */
    public string $doi = "";

    /**
     * @var string $outdir The output directory that the readme file should be saved in
     */
    public string $outdir = "";

    /**
     * Specify options available to console command provided by this controller.
     * Returns a list of controller class's public properties
     * 
     * @var string $actionID blah
     */
    public function options($actionID)
    {
        return ['doi', 'outdir'];
    }

    /**
     * This command will auto-create a readme file for a dataset given a doi
     *
     *  Usage:
     *      ./yii readme/create --doi
     * @throws \Throwable
     * @return int Exit code
     */
    public function actionCreate(): int
    {
        $optDoi = $this->doi;
        $optOutdir = $this->outdir;
        
        //Return usage unless mandatory options are passed
        if(!($optDoi)) {
            $this->stdout(
                "\nUsage:\n\t./yii readme/create --doi 100142 | --outdir /home/curators".PHP_EOL
            );
            return ExitCode::USAGE;
        }

        try {
            echo $this->writeReadme($optDoi, $optOutdir);
        }
        catch (Exception $e) {
            $this->stdout($e->getMessage().PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::DATAERR;
        }
        return ExitCode::OK;
    }

    /**
     * For outputting readme file
     * 
     * @throws Exception
     */
    private function writeReadme($doi, $outdir = null): string
    {
        // Check dataset exists otherwise throw exception to exit
        $dataset = Dataset::findOne(['identifier' => $doi]);
        if (!$dataset)
            throw new Exception("Dataset $doi not found");

        // Use array to store readme information
        $readme = [ "[DOI] 10.5524/$doi".PHP_EOL, 
            "[Title] $dataset->title".PHP_EOL,
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
            if ($i == $last_index)
                $citation .= "$full_name ";
            else
                $citation .= "$full_name; ";
        }

        $publication_year = substr($dataset->publication_date, 0, 4);
        $citation .= "($publication_year): ";
        $citation .= "$dataset->title GigaScience Database. http://dx.doi.org/10.5524/$doi".PHP_EOL;
        $readme[] = $citation;

        // [Data Type]
        $dataset_type = "[Data Type] ";
        // Returns array of DatasetType objects
        $datasetTypes = $dataset->datasetTypes;
        for ($i = 0; $i < count($datasetTypes); $i++) {
            // $type is an ActiveQuery object
            $type = $datasetTypes[$i]->getType();
            $typeName = $type->one()->name;
            $last_index = count($datasetTypes)-1;
            if ($i == $last_index)
                $dataset_type .= $typeName.PHP_EOL;
            else
                $dataset_type .= "$typeName,";
        }
        $readme[] = $dataset_type;

        // [Dataset Summary]
        $readme[] = "[Data Summary] $dataset->description".PHP_EOL;

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
        $readme[] = "[License]".PHP_EOL."All files and data are distributed under the Creative Commons Attribution-CC0 License unless specifically stated otherwise, see http://gigadb.org/site/term for more details.".PHP_EOL;

        // [Comments]
        $readme[] = "[Comments]".PHP_EOL;

        //[End]
        $readme[] = "[End]".PHP_EOL;

        // Convert readme array to string
        $readmeString = implode(PHP_EOL, $readme);

        // Save file if output directory exists
        if($outdir != null && is_dir($outdir)) {
            $filename = "$outdir/readme_$doi.txt";
            file_put_contents($filename, $readmeString);
        }
        return $readmeString;
    }
}