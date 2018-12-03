<?php
/**
 * Unit tests for FormattedDatasetAccessions that create HTML snippets for Dataset accessions
 *
 * @see DatasetAccessionsInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FormattedDatasetAccessionsTest extends CDbTestCase
{
	protected $fixtures=array(
        'datasets'=>'Dataset',
        'links'=>'Link',
        'prefixes'=>'Prefix',
    );

	public function setUp()
	{
		parent::setUp();
	}

	/**
	 * test that this Presentation class return an HTML snippet for all primary links
	 *
	 */
	public function testFormattedReturnsPrimaryLinks()
	{

		$doi = 100243;
		//we first need to stub an object for the cache
		$cache = $this->createMock(CApcCache::class);

        //then we set our stub for a Cache Hit
        $cache->method('get')
                 ->with($this->equalTo("dataset_${doi}_accessionsPrimaryLinks"))
                 ->willReturn([$this->links(0), $this->links(1)]);


		//we need to create a stub for the CWebUser object that's used when we call: Yii::app()->user->isGuest
        $current_user = $this->createMock(CWebUser::class);

        //we set the stubbed method to return false as we are testing the auhtorisation accepted scenario (user is logged in)
        $current_user->method('getIsGuest')
                 ->willReturn(false);

        $current_user->method('getState')
                 ->with("preferred_link")
                 ->willReturn("ENA");

        //setup our expected snippet:
        $expected_snippets=[];
        $expected_snippets[0] = new LinkWithFormat($this->links(0), 'ENA: <a target="_blank" href="http://www.ebi.ac.uk/ena/data/view/PRJEB225">PRJEB225</a><br>');
        $expected_snippets[1] = new LinkWithFormat($this->links(1), 'Link: <a target="_blank" href="http://www.ncbi.nlm.nih.gov/projects/SNP/snp_viewBatch.cgi?sbid=1056308">http://www.ncbi.nlm.nih.gov/projects/SNP/snp_viewBatch.cgi?sbid=1056308</a><br>');

		$dao_under_test = new FormattedDatasetAccessions(
								new AuthorisedDatasetAccessions(
									$current_user,
									new CachedDatasetAccessions(
										$cache,
										new StoredDatasetAccessions(
											$doi,
											$this->getFixtureManager()->getDbConnection()
										)
									)
								),
								'target="_blank"'
		);

		$primaryLinks = $dao_under_test->getPrimaryLinks();
		$nb_primaryLinks = count($primaryLinks);
		$this->assertEquals(2, $nb_primaryLinks);
		$counter = 0;
		while( $counter < $nb_primaryLinks ) {
			$this->assertEquals($expected_snippets[$counter]->format, $primaryLinks[$counter]->format );
			$counter++;
		}
	}

	/**
	 * test that this Presentation class return an HTML snippet for all secondary links
	 *
	 */
	public function testFormattedReturnsSecondaryLinks()
	{

		$doi = 100243;
		//we first need to stub an object for the cache
		$cache = $this->createMock(CApcCache::class);

        //then we set our stub for a Cache Hit
        $cache->method('get')
                 ->with($this->equalTo("dataset_${doi}_accessionsSecondaryLinks"))
                 ->willReturn([$this->links(2), $this->links(3), $this->links(4)]);


		//we need to create a stub for the CWebUser object that's used when we call: Yii::app()->user->isGuest
        $current_user = $this->createMock(CWebUser::class);

        //we set the stubbed method to return false as we are testing the auhtorisation accepted scenario (user is logged in)
        $current_user->method('getIsGuest')
                 ->willReturn(false);

        $current_user->method('getState')
                 ->with("preferred_link")
                 ->willReturn("ENA");

        //setup our expected snippet:
        $expected_snippets=[];
        $expected_snippets[0] = new LinkWithFormat($this->links(2), 'Link: <a target="_blank" href="http://www.ncbi.nlm.nih.gov/projects/SNP/snp_viewBatch.cgi?sbid=1056306">http://www.ncbi.nlm.nih.gov/projects/SNP/snp_viewBatch.cgi?sbid=1056306</a><br>');
        $expected_snippets[1] = new LinkWithFormat($this->links(3), 'SRA: <a target="_blank" href="http://www.ncbi.nlm.nih.gov/sra?term=SRP003590">SRP003590</a><br>');
        $expected_snippets[2] = new LinkWithFormat($this->links(4), 'GEO: <a target="_blank" href="#">GSE30337</a><br>'); //because GEO prefix is not in test database

		$dao_under_test = new FormattedDatasetAccessions(
								new AuthorisedDatasetAccessions(
									$current_user,
									new CachedDatasetAccessions(
										$cache,
										new StoredDatasetAccessions(
											$doi,
											$this->getFixtureManager()->getDbConnection()
										)
									)
								),
								'target="_blank"'
		);

		$secondaryLinks = $dao_under_test->getSecondaryLinks();
		$nb_secondaryLinks = count($secondaryLinks);
		$this->assertEquals(3, $nb_secondaryLinks);
		$counter = 0;
		while( $counter < $nb_secondaryLinks ) {
			$this->assertEquals($expected_snippets[$counter]->format, $secondaryLinks[$counter]->format );
			$counter++;
		}
	}

}
?>