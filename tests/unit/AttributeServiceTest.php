<?php

class AttributeServiceTest extends CTestCase
{
     /**
     * test it should replace keywords in the database with string of keywords
     *
     * @dataProvider keywordsProvider
     */
    public function testItShouldReplaceKeywords($keywords, $sanitized_keywords)
    {
        $dataset_id = 1;

         // Create a stub for the DatasetAttributesFactory class.
        $da_factory = $this->createMock(DatasetAttributesFactory::class);

        // Create a mock for the DatasetDAO class,
        $dao = $this->getMockBuilder(DatasetDAO::class)
                        ->setMethods(['removeKeywordsFromDatabaseForDatasetId', 'addKeywordsToDatabaseForDatasetIdAndString'])
                        ->setConstructorArgs([$da_factory])
                        ->getMock();


        // Instantiate a new Attribute service, the system under test.
        $service = new AttributeService($dao);



        // We expect removeKeywordsFromDatabaseForDatasetId and addKeywordsToDatabaseForDatasetIdAndString
        // to be called once each.
        // we expect the keywords to be sanitized before passed as an argument to the later method.

        $dao->expects($this->once())
                ->method('removeKeywordsFromDatabaseForDatasetId')
                ->with($this->equalTo($dataset_id));

        $dao->expects($this->once())
                ->method('addKeywordsToDatabaseForDatasetIdAndString')
                ->with($this->equalTo($dataset_id), $sanitized_keywords);

        // Execute the method to test
        $service->replaceKeywordsForDatasetIdWithString($dataset_id, $keywords);
    }

    public function keywordsProvider()
    {
        return [
            "no keyword" => ["", ""],
            "two clean keywords" => ["bam, dam", "bam, dam"],
            "two keywords and one dodgy" => ["am,<script>js:alert('boom')</script>, gram ",
                                             "am,&lt;script&gt;js:alert(&#039;boom&#039;)&lt;/script&gt;, gram"],
        ];
    }
}
