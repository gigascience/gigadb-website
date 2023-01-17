<?php 

class StoreDatasetConnectionsCest
{
    public function _before(FunctionalTester $I)
    {
    }

    // tests
    public function tryViewTheDatasetPageWithNotFoundIdentifier(FunctionalTester $I)
    {
        $I->amOnPage("/site/login");
        $I->submitForm('form.form-horizontal',[
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb']
        );
        $notFoundIdentifier = "10.1186/s13742-015-9999-9";
        $I->amOnPage("/adminManuscript/update/id/281");
        $I->fillField(['id' => 'Manuscript_identifier'], "$notFoundIdentifier");
        $I->click("Save");

        # go to the dataset page
        $I->amOnPage("/dataset/view/id/100142");
        $I->canSee("Supporting scripts and data for \"Investigation into the annotation of protocol sequencing steps in the Sequence Read Archive\".");
    }
}
