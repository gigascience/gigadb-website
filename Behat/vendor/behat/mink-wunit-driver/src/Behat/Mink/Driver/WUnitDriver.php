<?php

namespace Behat\Mink\Driver;

use WUnit\HttpKernel\Client;
use WUnit\Http\YiiKernel;

/*
 * This file is part of the Behat\Mink.
 * (c) Patrick Dreyer <patrick@dreyer.name>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * WUnit driver.
 *
 * @author Patrick Dreyer <patrick@dreyer.name>
 */
class WUnitDriver extends BrowserKitDriver
{
    /**
     * Initializes WUnit driver.
     *
     * @param Client $client HttpKernel client instance
     */
    public function __construct(Client $client = null)
    {
        parent::__construct($client ?: new Client(new YiiKernel()));
    }

    /**
     * Prepares URL for visiting.
     *
     * @param string $url
     *
     * @return string
     */
    protected function prepareUrl($url)
    {
        return $url;
    }
}
