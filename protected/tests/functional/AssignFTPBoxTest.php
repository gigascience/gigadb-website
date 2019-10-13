<?php
 /**
 * Test /adminDataset/assignFTPBox action that use FiledropService
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

class AssignFTPBoxTest extends FunctionalTesting
{

    use BrowserSignInSteps;
    use BrowserPageSteps;
    use CommonDataProviders;

    /**
     *
     * @uses \BrowserSignInSteps::loginToWebSiteWithSessionAndCredentialsThenAssert()
     */
    public function setUp()
    {
        parent::setUp();
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("admin@gigadb.org","gigadb","Admin");
    }

    public function testInstantiateAllDependenciesAndCallCreateAccount()
    {
        $api_endpoint = "http://fuw-admin-api/filedrop-accounts";
        $doi = "100002";

        // Prepare the http client to be traceable for testing

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);

        $webClient = new Client(['handler' => $stack]);

        // Instantiate FiledropService
        $filedropSrv = new FiledropService([
            "tokenSrv" => new TokenService(),
            "webClient" => $webClient,
            "requester" => \User::model()->findByPk(345),
            "identifier"=> $doi,
            ]);

        // invoke the action
        $url = "http://gigadb.dev/adminDataset/assignFTPBox/id/210/" ;
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, null);

        // test a HTTP call was actually made to the API
        $this->assertTrue(1 == count($container));
        $this->assertTrue("POST" == $container[0]['request']->getMethod());
        $this->assertTrue($api_endpoint == $container[0]['request']->getUri());

        // test that we are on the admin page after invocation of the action
        $this->assertEquals( "http://gigadb.dev/adminDataset/admin", $this->getCurrentUrl() );
    }

}

?>