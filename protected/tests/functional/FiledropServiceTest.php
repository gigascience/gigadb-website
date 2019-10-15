<?php
 /**
 * Test FiledropService to invoke on FUW REST APU the creation of filedrop account
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

class FiledropServiceTest extends FunctionalTesting
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

        //admin user is logged to gigadb
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("admin@gigadb.org","gigadb","Admin");

        $dotenv = Dotenv\Dotenv::create('/var/www', '.env');
        $dotenv->load();

        //backup ftp daemon config to restore after the test has run
        copy( "/etc/pure-ftpd/pureftpd.pdb", "/etc/pure-ftpd/pureftpd.pdb.bkp");
        copy( "/etc/pure-ftpd/passwd/pureftpd.passwd", "/etc/pure-ftpd/passwd/pureftpd.passwd.bkp");
    }

    public function tearDown()
    {
        //restore ftp daemon config to the prior state
        rename( "/etc/pure-ftpd/pureftpd.pdb.bkp", "/etc/pure-ftpd/pureftpd.pdb");
        rename( "/etc/pure-ftpd/passwd/pureftpd.passwd.bkp", "/etc/pure-ftpd/passwd/pureftpd.passwd");
        //remove directories for the dummy doi
        // rmdir("/home/uploader/000009");
        // rmdir("/home/downloader/000009");
        // rmdir("/home/credentials/000009");
    }

    public function testCreateAccountMakeAuthenticatedCall()
    {
        $api_endpoint = "http://fuw-admin-api/filedrop-accounts";
        $doi = "000009";
        $jwt_ttl = 31104000 ;

        // Prepare the http client to be traceable for testing

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);

        $webClient = new Client(['handler' => $stack]);

        // Instantiate FiledropService
        $filedropSrv = new FiledropService([
            "tokenSrv" => new TokenService([
                                  'jwtTTL' => $jwt_ttl,
                                  'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                                  'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                                  'users' => new UserDAO(),
                                  'dt' => new DateTime(),
                                ]),
            "webClient" => $webClient,
            "requester" => \User::model()->findByPk(344),
            "identifier"=> $doi,
            ]);

        // invoke the Filedrop Service
        try {

            $filedropSrv->createAccount();
        }
        catch(Exception $e) {
            // echo \GuzzleHttp\Psr7\str($e->getRequest());
        }

        // test an authenticated HTTP call was actually made to the API
        $this->assertTrue(1 == count($container));
        $this->assertTrue("POST" == $container[0]['request']->getMethod());
        $this->assertTrue($api_endpoint == $container[0]['request']->getUri());
        $this->assertFalse(401 == $container[0]['response']->getStatusCode());
        $this->assertFalse(403 == $container[0]['response']->getStatusCode());

        // test that we are on the admin page after invocation of the action
        // $this->assertEquals( "http://gigadb.dev/adminDataset/admin", $this->getCurrentUrl() );
    }

}

?>