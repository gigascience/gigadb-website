<?php

class CsvHelper
{
    const TYPE_CSV = 'text/csv';
    const TYPE_TSV = 'text/tab-separated-values';

    public static function getArrayByFileName($fileSource, $delimiter = ';')
    {
        $array = array();

        $row = 0;
        if (($handle = fopen($fileSource, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                $num = count($data);
                for ($c=0; $c < $num; $c++) {
                    $array[$row][$c] = $data[$c];
                }
                $row++;
            }
            fclose($handle);
        }

        return $array;
    }

    public static function getValidTypes()
    {
        return array(
            self::TYPE_CSV,
            self::TYPE_TSV,
        );
    }
}