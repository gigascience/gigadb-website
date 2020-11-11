<?php
/**
 * To generate citation in customized EndNote format in xml schema
 *
 * Class EndNoteHelper
 *
 *
 */

class EndNoteHelper
{
    public static function getRecords(string $full_doi)
    {
        $identifier = explode("/", $full_doi);
        return $identifier[1];

    }

}