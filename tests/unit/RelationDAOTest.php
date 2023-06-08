<?php

class RelationDAOTest extends CDbTestCase
{
    /**
     * @dataProvider relationshipProvider
     */
    public function testItshouldAddReciprocalRelation($relationship, $expected_reciprocal)
    {
        $relation_stub = $this->createMock(Relation::class);

        // Configure the stub for the relation for which to create a reciprocal relation
        $relation_stub->method('getRelatedDOI')
             ->willReturn('100249'); //of id 2
        $relation_stub->method('getDatasetID')
             ->willReturn('1'); // of identifier 100243
        $relation_stub->method('getRelationship')
             ->willReturn($relationship);

        // Create a mock for the Relation class,
        // only mock the setDatasetID, setRelatedDOI, setRelationship methods.
        $reciprocal_relation = $this->getMockBuilder(Relation::class)
                         ->setMethods(['setDatasetID', 'setRelatedDOI', 'setRelationship', 'save'])
                         ->getMock();

        // Set up the expectation for the setDatasetID method
        // to be called only once and with:
        // the id of the related dataset (of DOI 100249)
        // as its parameter.
        $reciprocal_relation->expects($this->once())
                 ->method('setDatasetID')
                 ->with($this->equalTo(2));

        // Set up the expectation for the setRelatedDOI method
        // to be called only once and with:
        // the DOI of the relating dataset (of id 1)
        // as its parameter.
        $reciprocal_relation->expects($this->once())
                 ->method('setRelatedDOI')
                 ->with($this->equalTo('100243'));

        // Set up the expectation for the setRelationship method
        // to be called only once and with:
        // the reciprocal relationship supplied by dataProvider
        // as its parameter.
        $reciprocal_relation->expects($this->once())
                 ->method('setRelationship')
                 ->with($this->equalTo($expected_reciprocal));

        // Set up the expectation for the save method
        // to be called only once
        $reciprocal_relation->expects($this->once())
                 ->method('save');

        $system_under_test = new RelationDAO() ;
        // createReciprocalTo make use of dependency injection for the reciprocal relation instantiation
        $system_under_test->createReciprocalTo($relation_stub, $reciprocal_relation);
    }

    public function relationshipProvider()
    {
        return [
            ["IsSupplementTo", "IsSupplementedBy"],
            ["IsSupplementedBy", "IsSupplementTo"],
            ["IsNewVersionOf", "IsPreviousVersionOf"],
            ["IsPreviousVersionOf", "IsNewVersionOf"],
            ["IsPartOf", "HasPart"],
            ["HasPart", "IsPartOf"],
            ["IsReferencedBy", "References"],
            ["References", "IsReferencedBy"],
        ];
    }
}
