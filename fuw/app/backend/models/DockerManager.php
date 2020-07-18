<?php

namespace backend\models;

use Yii;
use Docker\Docker;
use Docker\DockerClientFactory;
use Docker\API\Model\ContainerSummaryItem;
use Docker\API\Model\{ContainersIdExecPostBody,
                      ExecIdStartPostBody,
                    };

/**
 * A wrapper around Docker-PHP to execute commands on another container
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
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
	 * init(), called by Yii2. Here to initialise static variable $docker with a Docker client
	 *
	 */
	public function init()
	{
        if ( Yii::$app->params['docker_ssl'] ) {
            Yii::warning("DockerManager: using SSL client");
            $context = stream_context_create([
                'ssl' => Yii::$app->params['docker_ssl']
            ]);
            $client = DockerClientFactory::create([
                'remote_socket' => Yii::$app->params['remote_docker_hostname'],
                'stream_context' => $context,
                'ssl' => true,
            ]);

        }
        else {
            Yii::warning("DockerManager: using non-SSL client");
            $client = DockerClientFactory::create([
                'remote_socket' => Yii::$app->params['remote_docker_hostname'],
                'ssl' => false,
            ]);

        }


        $docker = Docker::create($client);
		if (null === $this->getClient() ) {
			$this->setClient( $docker ) ;
		}
	}

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
	 * @return null|\Docker\Docker a docker api client
	 */
	public function getClient(): ?\Docker\Docker
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
        try {
            $containers = $docker->containerList();
        }
        catch (Exception $e){
            Yii::error("remote_docker_hostname: ".Yii::$app->params['remote_docker_hostname']);
            Yii::error("DOCKER_HOST: ".getenv("DOCKER_HOST"));
            Yii::error($e->getMessage());
        }

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
     * @return null | object whatever is returned by execStart
     *
     */
    public function loadAndRunCommand(string $service, array $commandArray): ?object
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

    	return $result;
    }

    /**
     * Restart a contaimner
     *
     * @param string $service service to execute the command on
     *
     */
    public function restartContainer(string $service): void
    {
        $container = $this->getContainer("/${service}_1/");

        $result =  $this->getClient()->containerRestart( $container->getNames()[0] );
    }

}
?>