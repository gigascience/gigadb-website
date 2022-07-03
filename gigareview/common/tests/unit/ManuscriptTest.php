<?php
namespace common\tests;

use common\models\Manuscript;

class ManuscriptTest extends \Codeception\Test\Unit
{
    /**
     * @var \common\tests\UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testCanInsertValuesToTable()
    {
        $manuscript = new Manuscript();
        $this->assertNotNull($manuscript);
        $this->assertNull($manuscript->manuscript_number);
        $this->assertNull($manuscript->article_title);
        $this->assertNull($manuscript->revision_number);
        $this->assertNull($manuscript->created_at);
        $this->assertNull($manuscript->updated_at);
        $manuscript->save();
        $this->assertNotNull($manuscript->created_at);
        $manuscript->manuscript_number = "Test-GIGA-D-22-00060";
        $manuscript->article_title = "Test-title";
        $manuscript->revision_number = "1";
        sleep(1);
        $manuscript->save();
        $this->assertNotNull($manuscript->manuscript_number);
        $this->assertNotNull($manuscript->article_title);
        $this->assertNotNull($manuscript->revision_number);
        $this->assertGreaterThan($manuscript->created_at, $manuscript->updated_at);

    }
}