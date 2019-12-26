<?php

namespace common\tests\Helper;

/**
 * Before/After test hooks for the Db module goes here.
 *
 * Don't put any kind of before/after test hooks here.
 * Instead, subclass the module for which you want to create hooks
 * like here.
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class DbExtendedWithHooks extends \Codeception\Module\Db
{

    /**
     * HOOK: after each test scenario
     * make sure the fuw database's user table is cleaned of
     * record created during the tests
     * This is made necessary as default send email scenario
     * has now a side effect of adding a new user which
     * the Db module doesn't know how to remote after test run
     * TODO: figure out to have it run only for the scenario in question
    */
    public function _after(\Codeception\TestInterface $test)
    {
    	$this->amConnectedToDatabase('fuwdb');
    	$criteria = ['username' => 'joyfox'] ;
    	try {
            $this->_getDriver()->deleteQueryByCriteria('public.user', $criteria);
        } catch (\Exception $e) {
            $this->debug("Couldn't delete record " . json_encode($criteria) ." from public.user");
        }
        $this->amConnectedToDatabase(self::DEFAULT_DATABASE);
        parent::_after($test);
    }
}

 ?>