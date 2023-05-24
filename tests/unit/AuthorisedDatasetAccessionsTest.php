<?php

/**
 * Unit tests for AuthorisedDatasetAccessions to retrieve logged in user preferred link and add it to each dataset accessions
 *
 * @see DatasetAccessionsInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class AuthorisedDatasetAccessionsTest extends CDbTestCase
{
    protected $fixtures = array(
        'datasets' => 'Dataset',
        'links' => 'Link',
        'prefixes' => 'Prefix',
    );

    public function setUp()
    {
        parent::setUp();
    }


    /**
     * test that this DAO class return primary links  when user is a guest
     * where each link is wrapped in a facade class that add a property that return an empty preferred link source
     *
     */
    public function testAuthorisedReturnsPrimaryLinksGuestUser()
    {

        //we first need to stub an object for the cache
        $cachedDatasetAccessions = $this->createMock(CachedDatasetAccessions::class);

        //then we set our stub for a Cache Hit
        $cachedDatasetAccessions->method('getPrimaryLinks')
                 ->willReturn([$this->links(0), $this->links(1)]);


        //we need to create a mock for the CWebUser object that's used when we call: Yii::app()->user->isGuest
        $current_user = $this->getMockBuilder(CWebUser::class)
                         ->setMethods(['getIsGuest'])
                         ->getMock();

        //we set the mocked method to return false as we are testing the auhtorisation accepted scenario (user is logged in)
        $current_user->expects($this->once())
                 ->method('getIsGuest')
                 ->willReturn(true);

        //setup our expected results:
        $expected_links_with_preferred_source = [];
        array_push($expected_links_with_preferred_source, new LinkWithPreference($this->links(0), ''));
        array_push($expected_links_with_preferred_source, new LinkWithPreference($this->links(1), ''));

        $dao_under_test = new AuthorisedDatasetAccessions(
            $current_user,
            $cachedDatasetAccessions
        );

        $primaryLinks = $dao_under_test->getPrimaryLinks();
        $nb_primaryLinks = count($primaryLinks);
        $this->assertEquals(2, $nb_primaryLinks);
        $counter = 0;
        while ($counter < $nb_primaryLinks) {
            $this->assertEquals($expected_links_with_preferred_source[$counter]->link, $primaryLinks[$counter]->link);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->is_primary, $primaryLinks[$counter]->is_primary);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->preferred_source, $primaryLinks[$counter]->preferred_source);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->id, $primaryLinks[$counter]->id);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->dataset_id, $primaryLinks[$counter]->dataset_id);
            $counter++;
        }
    }

    /**
     * test that this DAO class return primary links  when user is logged in
     * where each link is wrapped in a facade class that add a property that return the user's preferred link source
     *
     */
    public function testAuthorisedReturnsPrimaryLinksLoggedInUser()
    {

        //we first need to stub an object for the cache
        $cachedDatasetAccessions = $this->createMock(CachedDatasetAccessions::class);

        //then we set our stub for a Cache Hit
        $cachedDatasetAccessions->method('getPrimaryLinks')
                 ->willReturn([$this->links(0), $this->links(1)]);


        //we need to create a mock for the CWebUser object that's used when we call: Yii::app()->user->isGuest
        $current_user = $this->getMockBuilder(CWebUser::class)
                         ->setMethods(['getIsGuest', 'getState'])
                         ->getMock();

        //we set the mocked methods to return false as we are testing the auhtorisation accepted scenario (user is logged in)
        $current_user->expects($this->once())
                 ->method('getIsGuest')
                 ->willReturn(false);

        $current_user->expects($this->once())
                 ->method('getState')
                 ->with("preferred_link")
                 ->willReturn("ENA");

        //setup our expected results:
        $expected_links_with_preferred_source = [];
        array_push($expected_links_with_preferred_source, new LinkWithPreference($this->links(0), 'ENA'));
        array_push($expected_links_with_preferred_source, new LinkWithPreference($this->links(1), 'ENA'));

        $dao_under_test = new AuthorisedDatasetAccessions(
            $current_user,
            $cachedDatasetAccessions
        );

        $primaryLinks = $dao_under_test->getPrimaryLinks();
        $nb_primaryLinks = count($primaryLinks);
        $this->assertEquals(2, $nb_primaryLinks);
        $counter = 0;
        while ($counter < $nb_primaryLinks) {
            $this->assertEquals($expected_links_with_preferred_source[$counter]->link, $primaryLinks[$counter]->link);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->is_primary, $primaryLinks[$counter]->is_primary);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->preferred_source, $primaryLinks[$counter]->preferred_source);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->id, $primaryLinks[$counter]->id);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->dataset_id, $primaryLinks[$counter]->dataset_id);
            $counter++;
        }
    }

    /**
     * test that this DAO class return primary links  when user is logged in but preferred source is null
     * where each link is wrapped in a facade class that add a property that return the user's preferred link source
     *
     */
    public function testAuthorisedReturnsPrimaryLinksLoggedInUserNullPreferredSource()
    {

        //we first need to stub an object for the cache
        $cachedDatasetAccessions = $this->createMock(CachedDatasetAccessions::class);

        //then we set our stub for a Cache Hit
        $cachedDatasetAccessions->method('getPrimaryLinks')
                 ->willReturn([$this->links(0), $this->links(1)]);


        //we need to create a mock for the CWebUser object that's used when we call: Yii::app()->user->isGuest
        $current_user = $this->getMockBuilder(CWebUser::class)
                         ->setMethods(['getIsGuest', 'getState'])
                         ->getMock();

        //we set the mocked methods to return false as we are testing the auhtorisation accepted scenario (user is logged in)
        $current_user->expects($this->once())
                 ->method('getIsGuest')
                 ->willReturn(false);

        $current_user->expects($this->once())
                 ->method('getState')
                 ->with("preferred_link")
                 ->willReturn(null);

        //setup our expected results:
        $expected_links_with_preferred_source = [];
        array_push($expected_links_with_preferred_source, new LinkWithPreference($this->links(0), ''));
        array_push($expected_links_with_preferred_source, new LinkWithPreference($this->links(1), ''));

        $dao_under_test = new AuthorisedDatasetAccessions(
            $current_user,
            $cachedDatasetAccessions
        );

        $primaryLinks = $dao_under_test->getPrimaryLinks();
        $nb_primaryLinks = count($primaryLinks);
        $this->assertEquals(2, $nb_primaryLinks);
        $counter = 0;
        while ($counter < $nb_primaryLinks) {
            $this->assertEquals($expected_links_with_preferred_source[$counter]->link, $primaryLinks[$counter]->link);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->is_primary, $primaryLinks[$counter]->is_primary);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->preferred_source, $primaryLinks[$counter]->preferred_source);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->id, $primaryLinks[$counter]->id);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->dataset_id, $primaryLinks[$counter]->dataset_id);
            $counter++;
        }
    }

    /**
     * test that this DAO class return secondary links  when user is a guest
     * where each link is wrapped in a facade class that add a property that return an empty preferred link source
     *
     */
    public function testAuthorisedReturnsSecondaryLinksGuestUser()
    {

        //we first need to stub an object for the cache
        $cachedDatasetAccessions = $this->createMock(CachedDatasetAccessions::class);

        //then we set our stub for a Cache Hit
        $cachedDatasetAccessions->method('getSecondaryLinks')
                 ->willReturn([$this->links(2), $this->links(3),$this->links(4)]);


        //we need to create a mock for the CWebUser object that's used when we call: Yii::app()->user->isGuest
        $current_user = $this->getMockBuilder(CWebUser::class)
                         ->setMethods(['getIsGuest'])
                         ->getMock();

        //we set the mocked method to return false as we are testing the auhtorisation accepted scenario (user is logged in)
        $current_user->expects($this->once())
                 ->method('getIsGuest')
                 ->willReturn(true);

        //setup our expected results:
        $expected_links_with_preferred_source = [];
        array_push($expected_links_with_preferred_source, new LinkWithPreference($this->links(2), ''));
        array_push($expected_links_with_preferred_source, new LinkWithPreference($this->links(3), ''));
        array_push($expected_links_with_preferred_source, new LinkWithPreference($this->links(4), ''));

        $dao_under_test = new AuthorisedDatasetAccessions(
            $current_user,
            $cachedDatasetAccessions
        );

        $secondaryLinks = $dao_under_test->getSecondaryLinks();
        $nb_secondaryLinks = count($secondaryLinks);
        $this->assertEquals(3, $nb_secondaryLinks);
        $counter = 0;
        while ($counter < $nb_secondaryLinks) {
            $this->assertEquals($expected_links_with_preferred_source[$counter]->link, $secondaryLinks[$counter]->link);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->is_primary, $secondaryLinks[$counter]->is_primary);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->preferred_source, $secondaryLinks[$counter]->preferred_source);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->id, $secondaryLinks[$counter]->id);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->dataset_id, $secondaryLinks[$counter]->dataset_id);
            $counter++;
        }
    }

    /**
     * test that this DAO class return secondary links  when user is logged in
     * where each link is wrapped in a facade class that add a property that return the user's preferred link source
     *
     */
    public function testAuthorisedReturnsSecondaryLinksLoggedInUser()
    {

        //we first need to stub an object for the cache
        $cachedDatasetAccessions = $this->createMock(CachedDatasetAccessions::class);

        //then we set our stub for a Cache Hit
        $cachedDatasetAccessions->method('getSecondaryLinks')
                 ->willReturn([$this->links(2), $this->links(3),$this->links(4)]);


        //we need to create a mock for the CWebUser object that's used when we call: Yii::app()->user->isGuest
        $current_user = $this->getMockBuilder(CWebUser::class)
                         ->setMethods(['getIsGuest', 'getState'])
                         ->getMock();

        //we set the mocked methods to return false as we are testing the auhtorisation accepted scenario (user is logged in)
        $current_user->expects($this->once())
                 ->method('getIsGuest')
                 ->willReturn(false);

        $current_user->expects($this->once())
                 ->method('getState')
                 ->with("preferred_link")
                 ->willReturn("ENA");

        //setup our expected results:
        $expected_links_with_preferred_source = [];
        array_push($expected_links_with_preferred_source, new LinkWithPreference($this->links(2), 'ENA'));
        array_push($expected_links_with_preferred_source, new LinkWithPreference($this->links(3), 'ENA'));
        array_push($expected_links_with_preferred_source, new LinkWithPreference($this->links(4), 'ENA'));

        $dao_under_test = new AuthorisedDatasetAccessions(
            $current_user,
            $cachedDatasetAccessions
        );

        $secondaryLinks = $dao_under_test->getSecondaryLinks();
        $nb_secondaryLinks = count($secondaryLinks);
        $this->assertEquals(3, $nb_secondaryLinks);
        $counter = 0;
        while ($counter < $nb_secondaryLinks) {
            $this->assertEquals($expected_links_with_preferred_source[$counter]->link, $secondaryLinks[$counter]->link);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->is_primary, $secondaryLinks[$counter]->is_primary);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->preferred_source, $secondaryLinks[$counter]->preferred_source);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->id, $secondaryLinks[$counter]->id);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->dataset_id, $secondaryLinks[$counter]->dataset_id);
            $counter++;
        }
    }


    /**
     * test that this DAO class return secondary links  when user is logged in but preferred source is null
     * where each link is wrapped in a facade class that add a property that return the user's preferred link source
     *
     */
    public function testAuthorisedReturnsSecondaryLinksLoggedInUserNullPreferredSource()
    {

        //we first need to stub an object for the cache
        $cachedDatasetAccessions = $this->createMock(CachedDatasetAccessions::class);

        //then we set our stub for a Cache Hit
        $cachedDatasetAccessions->method('getSecondaryLinks')
                 ->willReturn([$this->links(2), $this->links(3),$this->links(4)]);


        //we need to create a mock for the CWebUser object that's used when we call: Yii::app()->user->isGuest
        $current_user = $this->getMockBuilder(CWebUser::class)
                         ->setMethods(['getIsGuest', 'getState'])
                         ->getMock();

        //we set the mocked methods to return false as we are testing the auhtorisation accepted scenario (user is logged in)
        $current_user->expects($this->once())
                 ->method('getIsGuest')
                 ->willReturn(false);

        $current_user->expects($this->once())
                 ->method('getState')
                 ->with("preferred_link")
                 ->willReturn(null);

        //setup our expected results:
        $expected_links_with_preferred_source = [];
        array_push($expected_links_with_preferred_source, new LinkWithPreference($this->links(2), ''));
        array_push($expected_links_with_preferred_source, new LinkWithPreference($this->links(3), ''));
        array_push($expected_links_with_preferred_source, new LinkWithPreference($this->links(4), ''));

        $dao_under_test = new AuthorisedDatasetAccessions(
            $current_user,
            $cachedDatasetAccessions
        );

        $secondaryLinks = $dao_under_test->getSecondaryLinks();
        $nb_secondaryLinks = count($secondaryLinks);
        $this->assertEquals(3, $nb_secondaryLinks);
        $counter = 0;
        while ($counter < $nb_secondaryLinks) {
            $this->assertEquals($expected_links_with_preferred_source[$counter]->link, $secondaryLinks[$counter]->link);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->is_primary, $secondaryLinks[$counter]->is_primary);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->preferred_source, $secondaryLinks[$counter]->preferred_source);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->id, $secondaryLinks[$counter]->id);
            $this->assertEquals($expected_links_with_preferred_source[$counter]->dataset_id, $secondaryLinks[$counter]->dataset_id);
            $counter++;
        }
    }
}
