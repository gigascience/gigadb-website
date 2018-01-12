<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WUnit\HttpKernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\BrowserKit\History;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\HttpKernel\Client as BaseClient;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Client simulates a browser and makes requests to a Kernel object.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
class Client extends BaseClient
{
    /**
     * Constructor.
     *
     * @param HttpKernelInterface $kernel    An HttpKernel instance
     * @param array               $server    The server parameters (equivalent of $_SERVER)
     * @param History             $history   A History instance to store the browser history
     * @param CookieJar           $cookieJar A CookieJar instance to store the cookies
     */
    public function __construct(HttpKernelInterface $kernel, array $server = array(), History $history = null, CookieJar $cookieJar = null)
    {
        parent::__construct($kernel, $server, $history, $cookieJar);

        $this->followRedirects = true;
    }

    /**
     * Restarts the client.
     *
     * Unfortunately, the base class does not fully reset (server parameters are not reset).
     *
     * @see Symfony\Component\HttpKernel\Client
     */
    public function restart()
    {
        parent::restart();
        $this->setServerParameters(array());
    }

    /**
     * Returns the script to execute when the request must be insulated.
     *
     * @param Request $request A Request instance
     *
     * @return string
     */
    protected function getScript($request)
    {
        $app = str_replace("'", "\\'", serialize(\Yii::app()));
        $request = str_replace("'", "\\'", serialize($request));
        $includePaths = get_include_path();

        $out = <<<EOF
<?php

define('_PHP_INSULATE_', true);
set_include_path('$includePaths');
require_once 'bootstrap.php';

\$request = unserialize('$request');

\$kernel = new \WUnit\Http\YiiKernel();
\$response = \$kernel->handle(\$request);
echo serialize(\$response);
EOF;

        return $out;
    }
}
