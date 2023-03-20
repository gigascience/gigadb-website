<?php

namespace Helper;

use Yii;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Functional extends \Codeception\Module
{
    /**
     * Calculate md5 checksum of response content of current page
     *
     * @return string
     * @throws \Codeception\Exception\ModuleException
     */
    public function checksumOfResponse(): string
    {
        return md5($this->getModule('PhpBrowser')->_getResponseContent());
    }

    /**
     * Method to delete rows in the database based on criteria. Intended to be used in _after() methods.
     *
     * @param string $table
     * @param array $criteria
     * @return void
     */
    public function deleteRowByCriteria(string $table, array $criteria): void
    {
        try {
            $this->getModule('Db')->_getDriver()->deleteQueryByCriteria($table, $criteria);
        } catch (\Exception $e) {
            $this->debug("Couldn't delete record " . json_encode($criteria) . " from $table");
        }
    }

    /**
     * Method to get the query results from the invalidation query in the main config file
     *
     * @param $dataset_id
     * @return array
     */
    public function getLatestCreateUsingQueryFromMainConfigFile($dataset_id): array
    {
        $mainConfig = require("/var/www/protected/config/main.php");
        $invalidationQuery = preg_replace("/@id/", $dataset_id, $mainConfig['params']['cacheConfig']['DatasetComponents']['invalidationQuery']);
        if ($invalidationQuery !== null) {
            try {
                $invalidationQuery = preg_replace("/@id/", $dataset_id, $invalidationQuery);
                return $this->getModule('Db')->_getDriver()->executeQuery($invalidationQuery, [])->fetchAll();
            } catch (\Exception $e) {
                $this->debug("Couldn't execute invalidation query: " . $e->getMessage());
                return [];
            }
        } else {
            $this->debug("Invalidation query not found");
        }
        return [];
    }
}
