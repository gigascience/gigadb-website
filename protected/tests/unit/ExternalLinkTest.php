<?php


class ExternalLinkTest extends CDbTestCase
{

    function testLoadByData()
    {
        $data = array (
            'dataset_id' => 1,
            'url' => 'doi:10.1093/gigascience/giy095',
            'externalLinkType' => AIHelper::MANUSCRIPTS,
            'externalLinkDescription' => 'test description',
        );

        $exLink = new ExternalLink();
        $exLink->loadByData($data);

        $this->assertEquals(1, $exLink->dataset_id);
        $this->assertEquals('doi:10.1093/gigascience/giy095', $exLink->url);
        $this->assertEquals(AIHelper::MANUSCRIPTS, $exLink->external_link_type_id);
        $this->assertEquals('test description', $exLink->description);
    }

    function testValidateManuscripts()
    {
        $data = array (
            'dataset_id' => 1,
            'url' => 'doi:10.1093/gigascience/giy095',
            'externalLinkType' => AIHelper::MANUSCRIPTS,
            'externalLinkDescription' => 'test description',
        );

        $exLink = new ExternalLink();
        $exLink->loadByData($data);

        $this->assertTrue($exLink->validate());
    }

    function testValidateProtocols()
    {
        $data = array (
            'dataset_id' => 1,
            'url' => 'doi:10.17504/protocols.io.gk8buzw',
            'externalLinkType' => AIHelper::PROTOCOLS,
            'externalLinkDescription' => null,
        );

        $exLink = new ExternalLink();
        $exLink->loadByData($data);

        $this->assertTrue($exLink->validate());
    }

    function testValidate3dImages()
    {
        $data = array (
            'dataset_id' => 1,
            'url' => 'https://skfb.ly/69wDV',
            'externalLinkType' => AIHelper::_3D_IMAGES,
            'externalLinkDescription' => null,
        );

        $exLink = new ExternalLink();
        $exLink->loadByData($data);

        $this->assertTrue($exLink->validate());
    }

    function testValidateCodes()
    {
        $data = array (
            'dataset_id' => 1,
            'url' => '<script src="https://codeocean.com/widget.js?id=0a812d9b-0ff3-4eb7-825f-76d3cd049a43" async></script>',
            'externalLinkType' => AIHelper::CODES,
            'externalLinkDescription' => null,
        );

        $exLink = new ExternalLink();
        $exLink->loadByData($data);

        $this->assertTrue($exLink->validate());
    }

    function testValidateSources()
    {
        $data = array (
            'dataset_id' => 1,
            'url' => 'doi:12.3456/789012.3',
            'externalLinkType' => AIHelper::SOURCES,
            'externalLinkDescription' => 'test description',
        );

        $exLink = new ExternalLink();
        $exLink->loadByData($data);

        $this->assertTrue($exLink->validate());
    }
}
