<?php

class AuthorTest extends CDbTestCase
{
    protected $fixtures = array(
        'authors' => 'Author',
        'relationship' => 'Relationship',
        'author_rel' => 'AuthorRel',
    );



    function testSurname()
    {
        $expectations = array ("Muñoz",
                                 "Montana",
                                 "Martinez-Cruzado",
                                 "Potato Genome Sequencing Consortium",
                                 "Gilbert",
                                 "Régime",
                                 "Schiøtt") ;
        foreach ($expectations as $indice => $expectation) {
            $this->assertEquals($expectation, $this->authors($indice)->getSurname(), "Surname is returned");
        }
    }

    function testInitials()
    {
        $expectations = array("ÁGG", "CÁG", "Jc", "", "MTP", "JÉ", "M") ;
        foreach ($expectations as $indice => $expectation) {
            $this->assertEquals($expectation, $this->authors($indice)->getInitials(), "Initials is returned in correct format");
        }
    }


    function testDisplayNameSubstring()
    {
        $expectations = array("Muñoz ÁGG",
                             "Montana CÁG",
                             "Martinez-Cruzado Jc", //sometimes a name is meant to stay lowercase even in initial form
                             "Potato Genome Sequencing Consortium",
                             "Gilbert MTP",
                             "Régime JÉ",
                             "Schiøtt M",
                             "T.E Lawrence") ;
        foreach ($expectations as $indice => $expectation) {
            $this->assertEquals($expectation, $this->authors($indice)->getDisplayName(), "First names with accentuated character display correctly (#82), calculated and custom names too");
        }
    }


    function testFindAttachedAuthorByUserIdWhenAttached()
    {
        $expectation = "Martinez-Cruzado Jc";
        $this->assertEquals(
            $expectation,
            Author::findAttachedAuthorByUserId(345)->getDisplayName(),
            "return author attached to user"
        );
    }

    function testFindAttachedAuthorByUserIdWhenNotAttached()
    {
        $expectation = null;
        $this->assertEquals(
            $expectation,
            Author::findAttachedAuthorByUserId(344),
            "return no author attached to user"
        );
    }


    function testCanReturnIdenticalAuthors()
    {

        $this->assertEquals(array(3,4), $this->authors(1)->getIdenticalAuthors(), "return list of identical authors for A2");
        $this->assertEquals(array(), $this->authors(0)->getIdenticalAuthors(), "return list of identical authors for A1");
        $this->assertEquals(array(2,3), $this->authors(3)->getIdenticalAuthors(), "return list of identical authors for A4");
        $this->assertEquals(array(), $this->authors(8)->getIdenticalAuthors(), "return list of identical authors for A9");
        $this->assertEquals(array(6,7,8), $this->authors(4)->getIdenticalAuthors(), "return list of identical authors for A5");
        $this->assertEquals(array(5,7,8), $this->authors(5)->getIdenticalAuthors(), "return list of identical authors for A6");
        $this->assertEquals(array(5,6,8), $this->authors(6)->getIdenticalAuthors(), "return list of identical authors for A7");
        $this->assertEquals(array(5,6,7), $this->authors(7)->getIdenticalAuthors(), "return list of identical authors for A8");
        $this->assertEquals(array(10), $this->authors(10)->getIdenticalAuthors(), "return list of identical authors for A11");
        $this->assertEquals(array(11), $this->authors(9)->getIdenticalAuthors(), "return list of identical authors for A10");
    }

    function testCanMergeAuthorToAuthor()
    {
            $this->getFixtureManager()
        ->dbConnection
        ->createCommand("SELECT setval('author_rel_id_seq', max(id)) FROM author_rel;")
        ->execute();
        $this->assertEquals(array(), $this->authors(0)->getIdenticalAuthors(), "return list of identical authors for A1");
        $this->assertEquals(array(), $this->authors(8)->getIdenticalAuthors(), "return list of identical authors for A9");
        $is_success = $this->authors(0)->mergeAsIdenticalWithAuthor(9);  //merging A1 with A9
        $this->assertEquals(true, $is_success, "Can Merge an author to an author");
        $this->assertEquals(array(9), $this->authors(0)->getIdenticalAuthors(), "return list of identical authors for A1");
        $this->assertEquals(array(1), $this->authors(8)->getIdenticalAuthors(), "return list of identical authors for A9");
    }

    function testCanMergeAuthorToGraph()
    {
        $this->getFixtureManager()
        ->dbConnection
        ->createCommand("SELECT setval('author_rel_id_seq', max(id)) FROM author_rel;")
        ->execute();
        //We want to merge A9 with A10, given we already have: A9, and {A10, A11}
        $is_success = $this->authors(8)->mergeAsIdenticalWithAuthor(10); //merging A9 with A10
        $this->assertEquals(true, $is_success, "Can Merge an author with success");
        $this->assertEquals(array(10,11), $this->authors(8)->getIdenticalAuthors(), "return list(A10,A11) of identical authors for A9");
        $this->assertEquals(array(9,11), $this->authors(9)->getIdenticalAuthors(), "return list(A9,A11) of identical authors for A10");
        $this->assertEquals(array(9,10), $this->authors(10)->getIdenticalAuthors(), "return list(A9,A10) of identical authors for A11");

        //We want to merge A1 with A10, given we already have: {A9, A10, A11}
        $is_success = $this->authors(0)->mergeAsIdenticalWithAuthor(10); //merging A1 with A10
        $this->assertEquals(true, $is_success, "Can Merge an author with success");
        $this->assertEquals(array(9,10,11), $this->authors(0)->getIdenticalAuthors(), "return list(A9,A10,A11) of identical authors for A1");
        $this->assertEquals(array(1,10,11), $this->authors(8)->getIdenticalAuthors(), "return list(A1,A10,A11) of identical authors for A9");
        $this->assertEquals(array(1,9,11), $this->authors(9)->getIdenticalAuthors(), "return list(A1,A10,A11) of identical authors for A10");
        $this->assertEquals(array(1,9,10), $this->authors(10)->getIdenticalAuthors(), "return list(A1,A9,A10) of identical authors for A11");
    }


    function testCannotMergeWithNonExistentAuthor()
    {
        $is_success = $this->authors(0)->mergeAsIdenticalWithAuthor(22); //merging A1 with non existing id
        $this->assertEquals(false, $is_success, "Can Merge an author with success");
    }

    function testAuthorRelShouldBeAnIdenticalToRelationship()
    {
        $this->getFixtureManager()
        ->dbConnection
        ->createCommand("SELECT setval('author_rel_id_seq', max(id)) FROM author_rel;")
        ->execute();
        $is_success = $this->authors(0)->mergeAsIdenticalWithAuthor(9);  //merging A1 with A9
        $this->assertEquals(true, $is_success, "Can Merge an author to an author");
        $author_rel = AuthorRel::model()->findByAttributes(array("author_id" => 1,"related_author_id" => 9));
        $this->assertNotNull($author_rel, "An AuthorRel(1,9) exists");
        $this->assertEquals(21, $author_rel->relationship_id, "AuthorRel(1,9) is an 'IsIdenticalTo' relationship");
    }

    function testOnlyReturnsIdenticalToAuthorRel()
    {
        $this->getFixtureManager()
        ->dbConnection
        ->createCommand("SELECT setval('author_rel_id_seq', max(id)) FROM author_rel;")
        ->execute();
        $wrong_rel = new AuthorRel();
        $wrong_rel->author_id = 2;
        $wrong_rel->related_author_id = 1;
        $wrong_rel->relationship_id = 999 ;
        $this->assertTrue($wrong_rel->save(), "saving control AuthorRel");
        $this->assertEquals(array(3,4), $this->authors(1)->getIdenticalAuthors(), "return set {A3,A4} of identical authors for A2");
    }


    function testCannotMergeAuthorIfAlreadyInGraph()
    {
        $is_success = $this->authors(4)->mergeAsIdenticalWithAuthor(7); //should fail as A5 is already in {A5,A6,A7,A8}
        $this->assertFalse($is_success, "Won't merge as author already in graph");
    }

    function testCanMergeGraphToGraph()
    {
        $this->getFixtureManager()
        ->dbConnection
        ->createCommand("SELECT setval('author_rel_id_seq', max(id)) FROM author_rel;")
        ->execute();
        $is_success = $this->authors(3)->mergeAsIdenticalWithAuthor(11); // want to merge A4 of {A2,A3,A4} with A11 of {A10,A11}
        $this->assertEquals(true, $is_success, "Can Merge an author to an author");
        $this->assertEquals(array(2,3,10,11), $this->authors(3)->getIdenticalAuthors(), "return {A2,A3,A4,A11) of identical authors for A4");
        $this->assertEquals(array(2,3,4,11), $this->authors(9)->getIdenticalAuthors(), "return {A2,A3,A4,A11) of identical authors for A10");
        $this->assertEquals(array(2,3,4,10), $this->authors(10)->getIdenticalAuthors(), "return {A2,A3,A4,A11) of identical authors for A11");
    }


    function testCanUnmergeAuthorFromGraph()
    {
        $this->assertEquals(array(5,7,8), $this->authors(5)->getIdenticalAuthors(), "return {A5,A7,A8} of identical authors for A6");
        $is_success = $this->authors(5)->unMerge();
        $this->assertEquals(true, $is_success, "Can unMerge an author from a graph");
        $this->assertEquals(array(), $this->authors(5)->getIdenticalAuthors(), "return {} of identical authors for A6");
        $this->assertEquals(array(7,8), $this->authors(4)->getIdenticalAuthors(), "return {A7,A8} of identical authors for A5");
        $this->assertEquals(array(5,8), $this->authors(6)->getIdenticalAuthors(), "return {A5,A8} of identical authors for A7");
        $this->assertEquals(array(5,7), $this->authors(7)->getIdenticalAuthors(), "return {A5,A7} of identical authors for A8");
    }

    function testCanUnmergeAuthorFromAuthor()
    {
        $this->assertEquals(array(10), $this->authors(10)->getIdenticalAuthors(), "return {A10} of identical authors for A11");
        $this->assertEquals(array(11), $this->authors(9)->getIdenticalAuthors(), "return {A11} of identical authors for A10");
        $is_success = $this->authors(9)->unMerge();
        $this->assertEquals(true, $is_success, "Can unMerge an author from an author");
        $this->assertEquals(array(), $this->authors(10)->getIdenticalAuthors(), "return {} of identical authors for A11");
        $this->assertEquals(array(), $this->authors(9)->getIdenticalAuthors(), "return {} of identical authors for A10");
    }

    function testCanReturnIdenticalAuthorsDisplayName()
    {
        $this->assertEquals(array("Régime JÉ", "Schiøtt M", "T.E Lawrence"), $this->authors(4)->getIdenticalAuthorsDisplayName(), "return display names of identical authors for A5");
    }

    function testCanCompareWithAuthor()
    {
        $this->assertTrue($this->authors(3)->IsIdenticalTo(3), "A4 is identical to A3");
        $this->assertTrue($this->authors(3)->IsIdenticalTo(2), "A4 is identical to A2");
        $this->assertFalse($this->authors(3)->IsIdenticalTo(5), "A4 is not identical to A5");
        $this->assertFalse($this->authors(3)->IsIdenticalTo(9), "A4 is not identical to A9");
        $this->assertTrue($this->authors(3)->IsIdenticalTo(4), "A4 is identical to A4 (itself)");
    }

    function testCanReturnedAuthorDetails()
    {
        $this->assertEquals("10. Sam H Bert (Orcid: 0000-X478-1087)", $this->authors(9)->getAuthorDetails(), "Author details returned for A10");
        $this->assertEquals("1. Ángel GG Muñoz (Orcid: n/a)", $this->authors(0)->getAuthorDetails(), "Author details returned for A1");
        $this->assertEquals("7. Morten Schiøtt (Orcid: n/a)", $this->authors(6)->getAuthorDetails(), "Author details returned for A7");
    }
}
