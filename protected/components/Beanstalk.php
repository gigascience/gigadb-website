<?php
use Beanstalk\Client;

/**
 * Wrapper class for davidpersson's beanstalk library which enables minimal,
 * distributed server awareness and recover in the event of a failure.
 *
 * <https://github.com/davidpersson/beanstalk>
 */
class Beanstalk extends CApplicationComponent
{
  	public $servers = [];
	public $defaults = [
			'persistent' => true,
			'host' => '127.0.0.1',
			'port' => 11300,
			'timeout' => 1,
			'logger' => null
	];
  	protected $_servers = [];

	public function init()
	{
		parent::init();

		if (!is_array($this->servers))
			$this->servers = [];
	}

	/**
	 * Gets server name based upon weighting algorithm, or by name if requested.
	 * If the client cannot be connected to then it will not be added.
	 *
	 * Failed connections will cause this method to be called recursively (not the best), but
	 * will ultimately result in more attempts against this method until a server is resolved.
	 */
	public function getClient($serverName=null)
	{
		// the reason for this naming is that we are really getting a client connection to a server
		// and it needs to be clear that we could have more than 1 server.
		if( empty($serverName) )
			$serverName = $this->getServer();

		if( !isset($this->_servers[$serverName]) )
		{
			$server = $this->servers[$serverName];
			if (!isset($server['port']))
				$server['port'] = $this->defaults['port'];

			$client = new Client([
				'host'=>$server['host'],
				'port'=>$server['port'],
				'timeout'=>isset($server['connectTimeout']) ? $server['connectTimeout'] : null,
			]);

			// check connectivity before adding
			if( $client->connect() )
			{
				$this->_servers[$serverName] = $client;
				return $client;
			}
			// this means we should remove this server because it cannot be reached temporarily
			else
				unset($this->servers[$serverName]);
		}
		// already connected (persistent);
		// we should check the connection just to be sure before handing it off.
		else
		{
			if( $this->_servers[$serverName]->connected )
				return $this->_servers[$serverName];
		}
		// recursive call here until we get a good server???
		return $this->getClient();
	}

	/**
	 * Returns the specified server to the user.
	 */
	public function getServerByName($name)
	{
		if( array_key_exists($this->servers[$name]) )
			return $name;
		else
			throw new CException('Invalid server name.');
	}

	/**
	 * Returns the name of the server to be used.
	 * If only one exists it will be returned by name, otherwise it
	 * used weighted algorithm to determine which server to use.
	 */
	public function getServer()
	{
		if( count($this->servers) == 0 )
			throw new CException('Unable to establish connection with beanstalkd server.');
		//if only 1 available server, return that one, otherwise return by weight
		if( is_array($this->servers) && count($this->servers) == 1 )
			return array_keys($this->servers)[0];
		// return server name by using weighted algorithm
		else
			return $this->getRandomWeightedElement($this->getServerWeights());
	}

	/**
	 * Parses through server array and returns server names and their appropriate weights as an array.
	 * @returns array
	 */
	private function getServerWeights()
	{
		$ret = array();
		foreach($this->servers as $k=>$server)
		{
			if( !isset($server['weight']) )
				throw new CException('[weight] is a required fieldname for server.');

			$ret[$k] = $server['weight'];
		}
		return $ret;
	}

	/**
	 * Utility function for getting random values with weighting.
	 * Pass in an associative array, such as array('A'=>5, 'B'=>45, 'C'=>50)
	 * An array like this means that "A" has a 5% chance of being selected, "B" 45%, and "C" 50%.
	 * The return value is the array key, A, B, or C in this case.  Note that the values assigned
	 * do not have to be percentages.  The values are simply relative to each other.  If one value
	 * weight was 2, and the other weight of 1, the value with the weight of 2 has about a 66%
	 * chance of being selected.  Also note that weights should be integers.
	 *
	 * This function is a modified copy of that which is provided on the following page:
	 * <http://stackoverflow.com/questions/445235/generating-random-results-by-weight-in-php>
	 * Please contact me directly if there are any further legal requirements to use this code.
	 *
	 * @param array $weightedValues
	 */
	private function getRandomWeightedElement(array $weightedValues)
	{
		$randomNumber = mt_rand(1, (int) array_sum($weightedValues));

		foreach($weightedValues as $key => $value)
		{
			$randomNumber -= $value;
			if( $randomNumber <= 0 )
				return $key;
		}
	}
}
?>
