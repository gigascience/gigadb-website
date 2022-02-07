<?php
/**
 * Command to change individual config values
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class ConfigChangeCommand extends CConsoleCommand
{

    public function getHelp()
    {
        $helpText = "Change limit for search results" . PHP_EOL;
        $helpText .= "Usage: ./protected/yiic configchange searchresult --limit=5" . PHP_EOL;

        return $helpText;
    }

    /**
     * Replace the value for search_result_limit with one passed as parameter
     *
     * @param $limit
     */
    public function actionSearchResult($limit)
    {
        shell_exec("sed -i  \"s/'search_result_limit' => '[[:digit:]]*'/'search_result_limit' => '$limit'/\" /var/www/protected/config/local.php");
    }
}