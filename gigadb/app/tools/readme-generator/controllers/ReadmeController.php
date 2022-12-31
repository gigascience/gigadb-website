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
     * Specify options available to console command provided by this controller.
     * Returns a list of controller class's public properties
     * 
     * @var string $actionID blah
     */
    public function options($actionID)
    {
        return ['doi'];
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
        //Return usage unless mandatory options are passed
        if(!($optDoi)) {
            $this->stdout(
                "\nUsage:\n\t./yii readme/create --doi\n"
            );
            return ExitCode::USAGE;
        }

        try {
            echo $this->writeReadme($optDoi);
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
     * @throws Exception
     */
    private function writeReadme($doi): string
    {
        // Check dataset exists otherwise throw exception to exit
        $dataset = Dataset::findOne(['identifier' => $doi]);
        if (!$dataset) {
            throw new Exception("Dataset directory for $doi not found");
        }

        $readme = "";

        // Create [DOI]
        $citation_doi = "[DOI] 10.5524/$doi\n";
        $readme .= $citation_doi;

        // Create [Title]
        $title = "[Title] $dataset->title\n";
        $readme .= $title;

        // [Release Date]
        $release_date = "[Release Date] $dataset->publication_date\n";
        $readme .= $release_date;
            
        // [Citation]
        $citation = "[Citation] ";
        $authors = $dataset->authors;
        for ($i = 0; $i < count($authors); $i++) {
            $first_name_initial = substr($authors[$i]->first_name, 0, 1);
            $middle_name_initial = substr($authors[$i]->middle_name, 0, 1);
            $surname = $authors[$i]->surname;
            $full_name = "$surname, $first_name_initial$middle_name_initial";
            if ($i == count($authors)-1)
                $citation .= "$full_name ";
            else
                $citation .= "$full_name; ";
        }

        $publicaton_date = $dataset->publication_date;
        $publicaton_year = substr($publicaton_date, 0, 4);
        $citation .= "($publicaton_year): ";
        $citation .= "$dataset->title GigaScience Database. http://dx.doi.org/10.5524/$doi\n";
        $readme .= $citation;

        // [Data Type]
        $dataset_type = "[Data Type] ";
        // Returns array of DatasetType objects
        $datasetTypes = $dataset->datasetTypes;
        for ($i = 0; $i < count($datasetTypes); $i++) {
            // $type is an ActiveQuery object
            $type = $datasetTypes[$i]->getType();
            $typeName = $type->one()->name;
            if ($i == count($datasetTypes)-1)
                $dataset_type .= $typeName;
            else
                $dataset_type .= "$typeName,";
        }
        $readme .= "$dataset_type\n";

        // [Dataset Summary]
        $dataset_summary = "[Data Summary] $dataset->description";
        $readme .= "$dataset_summary\n";

        return $readme;
    }
}