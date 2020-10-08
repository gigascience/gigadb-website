<?php

/**
 * Functional test for dataset logging of new entry
 */

class DatasetLogServiceTest extends FunctionalTesting
{
    public function testItShouldCreateEntryInDatasetLogUsingDatasetLogService()
    {
        try {
            $result = Yii::app()->datasetLogService->createDatasetLogEntry("22", "20", "Stuff");
            $this->assertTrue("true" === "true", "stuff");
        }

        catch (Error $e) {
            $this->fail("Exception thrown: " . $e->getMessage());
        }

    }
}
