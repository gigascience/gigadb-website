<?php

class CsvHelper
{
    /**
     * @param string $fileSource
     * @param string $ext
     * @return array
     * @throws Exception
     */
    public static function parse($fileSource, $ext = 'csv')
    {
        if (!in_array($ext, static::getValidExtensions())) {
            throw new \Exception('File has wrong extension.');
        }

        $rows = array();

        $delimiter = static::detectDelimiter($fileSource);

        $row = 0;
        if (($handle = fopen($fileSource, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, $delimiter)) !== false) {
                $num = count($data);

                for ($c=0; $c < $num; $c++) {
                    $rows[$row][$c] = trim($data[$c]);
                }

                $row++;
            }

            fclose($handle);
        }

        if (!$rows) {
            throw new \Exception('File is empty.');
        }

        return $rows;
    }

    /**
     * @param $fileSource
     * @return false|int|string
     */
    protected static function detectDelimiter($fileSource)
    {
        $delimiters = array(
            ';' => 0,
            ',' => 0,
            "\t" => 0,
            "|" => 0
        );

        $handle = fopen($fileSource, "r");
        $firstLine = fgets($handle);
        fclose($handle);
        foreach ($delimiters as $delimiter => &$count) {
            $count = count(str_getcsv($firstLine, $delimiter));
        }

        return array_search(max($delimiters), $delimiters);
    }

    /**
     * @return array
     */
    protected static function getValidExtensions()
    {
        return array(
            'csv',
            'tsv',
        );
    }
}