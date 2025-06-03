<?php

declare(strict_types=1);

require_once __DIR__ . '/LoadingFixtureTrait.php';

use Codeception\Test\Unit;
/**
 * Test non getter/setter methods from the Dataset model class
 *
 * How to run:
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept run --debug unit DatasetAsXmlTest
 *
 *
**/
class DatasetAsXmlTest extends Unit
{
    use LoadingFixtureTrait;

    public function _before()
    {
        $db = $this->getModule('Db')->_getDbh();
        $db->exec('TRUNCATE TABLE gigadb_user CASCADE');
        $db->exec('TRUNCATE TABLE dataset CASCADE');
        $db->exec('TRUNCATE TABLE author CASCADE');
        $db->exec('TRUNCATE TABLE dataset_author CASCADE');
        $db->exec('TRUNCATE TABLE dataset_funder CASCADE');
        $db->exec('TRUNCATE TABLE dataset_project CASCADE');
        $db->exec('TRUNCATE TABLE dataset_sample CASCADE');
        $db->exec('TRUNCATE TABLE dataset_type CASCADE');
        $db->exec('TRUNCATE TABLE external_link CASCADE');
        $db->exec('TRUNCATE TABLE funder_name CASCADE');
        $db->exec('TRUNCATE TABLE external_link_type CASCADE');
        $db->exec('TRUNCATE TABLE manuscript CASCADE');
        $db->exec('TRUNCATE TABLE project CASCADE');
        $db->exec('TRUNCATE TABLE sample CASCADE');
        $db->exec('TRUNCATE TABLE species CASCADE');
        $db->exec('TRUNCATE TABLE type CASCADE');

        $this->loadFixture('gigadb_user', \User::class);
        $this->loadFixture('dataset', \Dataset::class);
        $this->loadFixture('author', \Author::class);
        $this->loadFixture('manuscript', \Manuscript::class);
        $this->loadFixture('project', \Project::class);
        $this->loadFixture('species', \Species::class);
        $this->loadFixture('sample', \Sample::class);
        $this->loadFixture('type', \Type::class);
        $this->loadFixture('dataset_author', \DatasetAuthor::class);
        $this->loadFixture('funder_name', \Funder::class);
        $this->loadFixture('dataset_funder', \DatasetFunder::class);
        $this->loadFixture('dataset_project', \DatasetProject::class);
        $this->loadFixture('dataset_sample', \DatasetSample::class);
        $this->loadFixture('dataset_type', \DatasetType::class);
        $this->loadFixture('external_link_type', \ExternalLinkType::class);
        $this->loadFixture('external_link', \ExternalLink::class);
    }

    public function testDatasetAAsXml()
    {
        $myDataset = Dataset::model()->findByPk(1);
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
        $this->assertEquals('IsPartOf', $relatedIdentifierLink->getAttribute('relationType'));
        $this->assertEquals('Project', $relatedIdentifierLink->getAttribute('resourceTypeGeneral'));
        $this->assertEquals('http://avian.genomics.cn/en/index.html', $relatedIdentifierLink->nodeValue);

        $relatedIdentifierProject = $dom->getElementsByTagName('relatedIdentifier')->item(8);
        $this->assertEquals('DOI', $relatedIdentifierProject->getAttribute('relatedIdentifierType'));
        $this->assertEquals('References', $relatedIdentifierProject->getAttribute('relationType'));
        $this->assertEquals('Workflow', $relatedIdentifierProject->getAttribute('resourceTypeGeneral'));
        $this->assertEquals('http://foo4.com', $relatedIdentifierProject->nodeValue);

        $relatedIdentifierExternalLink = $dom->getElementsByTagName('relatedIdentifier')->item(9);
        $this->assertEquals('URL', $relatedIdentifierExternalLink->getAttribute('relatedIdentifierType'));
        $this->assertEquals('References', $relatedIdentifierExternalLink->getAttribute('relationType'));
        $this->assertEquals('Other', $relatedIdentifierExternalLink->getAttribute('resourceTypeGeneral'));
        $this->assertEquals('http://foo5.com', $relatedIdentifierExternalLink->nodeValue);
    }
}
