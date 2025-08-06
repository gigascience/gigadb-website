<?php

declare(strict_types=1);

/**
 * Test non getter/setter methods from the Dataset model class
 *
 * How to run:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run --debug unit DatasetAsXmlTest
 *
 *
**/
class DatasetAsXmlTest  extends CDbTestCase
{
    protected $fixtures = array(
        'datasets' => 'Dataset',
        'authors' => 'Author',
        'dataset_authors' => 'DatasetAuthor',
        'dataset_funders' => 'DatasetFunder',
        'dataset_projects' => 'DatasetProject',
        'dataset_samples' => 'DatasetSample',
        'dataset_types' => 'DatasetType',
        'external_links' => 'ExternalLink',
        'funder_names' => 'Funder',
        'external_link_types' => 'ExternalLinkType',
        'manuscripts' => 'Manuscript',
        'projects' => 'Project',
        'samples' => 'Sample',
        'species' => 'Species',
        'types' => 'Type'
    );

    public function testUploadStatusValidation()
    {
        $myDataset = $this->datasets(0);
        $dom = new DomDocument();
        $dom->loadXML($myDataset->toXml());

        $creatorChildNodes = $dom->getElementsByTagName('creator')->item(0)->childNodes;
        $this->assertCount(3, $creatorChildNodes);
        $this->assertEquals('Morten, Schiøtt,', $creatorChildNodes->item(0)->nodeValue);
        $this->assertEquals('Morten', $creatorChildNodes->item(1)->nodeValue);
        $this->assertEquals('Schiøtt,', $creatorChildNodes->item(2)->nodeValue);

        $creatorChildNodes = $dom->getElementsByTagName('creator')->item(1)->childNodes;
        $this->assertCount(3, $creatorChildNodes);
        $this->assertEquals("Carlos, Ábel G, Montana,", $creatorChildNodes->item(0)->nodeValue);
        $this->assertEquals('Carlos, Ábel G', $creatorChildNodes->item(1)->nodeValue);
        $this->assertEquals('Montana,', $creatorChildNodes->item(2)->nodeValue);

        $relatedIdentifierManuscript = $dom->getElementsByTagName('relatedIdentifier')->item(0);
        $this->assertEquals('DOI', $relatedIdentifierManuscript->getAttribute('relatedIdentifierType'));
        $this->assertEquals('IsCitedBy', $relatedIdentifierManuscript->getAttribute('relationType'));
        $this->assertEquals('JournalArticle', $relatedIdentifierManuscript->getAttribute('resourceTypeGeneral'));
        $this->assertEquals('10.1186/gb-2012-13-10-r100', $relatedIdentifierManuscript->nodeValue);

        $relatedIdentifierLink = $dom->getElementsByTagName('relatedIdentifier')->item(3);
        $this->assertEquals('URL', $relatedIdentifierLink->getAttribute('relatedIdentifierType'));
        $this->assertEquals('References', $relatedIdentifierLink->getAttribute('relationType'));
        $this->assertEquals('Dataset', $relatedIdentifierLink->getAttribute('resourceTypeGeneral'));
        $this->assertEquals('http://www.ebi.ac.uk/ena/data/view/PRJEB225', $relatedIdentifierLink->nodeValue);

        $relatedIdentifierProject = $dom->getElementsByTagName('relatedIdentifier')->item(8);
        $this->assertEquals('URL', $relatedIdentifierProject->getAttribute('relatedIdentifierType'));
        $this->assertEquals('References', $relatedIdentifierProject->getAttribute('relationType'));
        $this->assertEquals('Other', $relatedIdentifierProject->getAttribute('resourceTypeGeneral'));
        $this->assertEquals('http://foo2.com', $relatedIdentifierProject->nodeValue);

        $relatedIdentifierExternalLink = $dom->getElementsByTagName('relatedIdentifier')->item(10);
        $this->assertEquals('DOI', $relatedIdentifierExternalLink->getAttribute('relatedIdentifierType'));
        $this->assertEquals('References', $relatedIdentifierExternalLink->getAttribute('relationType'));
        $this->assertEquals('Workflow', $relatedIdentifierExternalLink->getAttribute('resourceTypeGeneral'));
        $this->assertEquals('http://foo4.com', $relatedIdentifierExternalLink->nodeValue);
    }
}
