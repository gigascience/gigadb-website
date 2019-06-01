<?php

class CsvHelperTest extends CDbTestCase
{
    function testParseCsv() {
        $fileSourse = \MainHelper::getFilesDir() . '/csv/test_authors.csv';

        $rows = \CsvHelper::parse($fileSourse);

        $this->assertTrue(is_array($rows));
        $this->assertEquals(3, count($rows));
        $this->assertEquals('first name1', $rows[0][0]);
        $this->assertEquals('Contribution3', $rows[2][4]);
    }

    function testParseTsv() {
        $fileSourse = \MainHelper::getFilesDir() . '/csv/test_authors.tsv';

        $rows = \CsvHelper::parse($fileSourse);

        $this->assertTrue(is_array($rows));
        $this->assertEquals(3, count($rows));
        $this->assertEquals('first name4', $rows[0][0]);
        $this->assertEquals('Contribution1', $rows[2][4]);
    }
}
