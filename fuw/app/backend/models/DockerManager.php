<?php

namespace backend\models;

use Yii;
use Docker\Docker;
use Docker\API\Model\ContainerSummaryItem;
use Docker\API\Model\{ContainersIdExecPostBody,
                      ExecIdStartPostBody,
                    };

class DockerManager extends yii\base\BaseObject
{
	/**
     * @var array list of containers that should not be accessed programmatically
     */
    const FORBIDDEN = ["/console_1/","/db_1/"];

	/**
     * @var object $docker static class variable to hold connection to Docker
     */
	private static $docker;


	/**
	 * set a docker client
	 *
	 * @param  \Docker\Docker $client a docker api client
	 */
	public function setClient(\Docker\Docker $client): void
	{
        self::$docker = $client;
	}

	/**
	 * return a docker client
	 *
	 * @return \Docker\Docker a docker api client
	 */
	public function getClient(): \Docker\Docker
	{
        return self::$docker;
	}

	/**
	 * initialising the class
	 */
	// protected function init()
	// {
	// 	$this->setClient( Docker::create() );
	// }

	/**
     * Retrieve a matching container using Docker API
     *
     * @param string regex pattern for the container to match
     * @return null|\Docker\API\Model\ContainerSummaryItem container details
     */
    public function getContainer(string $containerPattern): ?\Docker\API\Model\ContainerSummaryItem
    {
        if( in_array($containerPattern, self::FORBIDDEN) ) {
            return null;
        }

        $docker = $this->getClient();
        $containers = $docker->containerList();
        foreach ($containers as $container) {
            if ( preg_match($containerPattern,implode("",$container->getNames())) ) {
                return $container;
            }
        }
        return null;
    }

    /**
     * Factory for making instance of *PostBody objet for Docker PHP API
     *
     * @param string $postBodyType type of postBody to make
     * @param array $commandArray array forming a command to execute on the container
     * @return null|\Docker\API\Model\ContainersIdExecPostBody|\Docker\API\Model\ExecIdStartPostBody
     */
    public function makePostBodyFor(string $postBodyType, array $commandArray = null): ?object
    {
        if ("execConfig" == $postBodyType) {
            $execConfig = new ContainersIdExecPostBody();
            $execConfig->setAttachStdout(true);
            $execConfig->setAttachStderr(true);
            $execConfig->setCmd($commandArray);
            return $execConfig;
        }
        elseif ("execStartConfig" == $postBodyType) {
            $execStartConfig = new ExecIdStartPostBody();
            $execStartConfig->setDetach(false);
            $execStartConfig->setTty(false);
            return $execStartConfig;
        }
        return null;
    }

    /**
     * Load and run a command on a Docker service
     *
     * @param string $service service to execute the command on
     * @param array $commandArray command and its argument
     * @return bool return true if successful, false otherwise
     *
     */
    public function loadAndRunCommand(string $service, array $commandArray): bool
    {

        $container = $this->getContainer("/${service}_1/");

        $execConfig = $this->makePostBodyFor("execConfig", $commandArray);

        $execConfigResponse = $this->getClient()->containerExec(
        											$container->getId(), $execConfig
        										);

        $execStartConfig =  $this->makePostBodyFor("execStartConfig");

        $result =  $this->getClient()->execStart(
        										$execConfigResponse->getId(), $execStartConfig
        									);
        if (null === $result) {
            return true;
        }

    	return false;
    }
}
?>