<?php
/**
 * To generate citation in customized EndNote format in xml schema
 *
 * Class EndNoteHelper
 *
 * The followings are the available model relations:
 * @property Dataset $dataset
 *
 */

class EndNoteHelper
{
    public static function getRecords($full_doi)
    {
        $splitFull = explode("/", $full_doi);
        $identifier = $splitFull[1];
        $dataset = Dataset::model()->findAllByAttributes(array("identifier"=>'100002'));
//        $dataset = Dataset::model()->findAll($criteria);
        file_put_contents('model.log', print_r($dataset, true));

        return $identifier;

    }

}