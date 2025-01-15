<?php 

class AdminDatasetImageCest
{
    public function _before(FunctionalTester $I)
    {
    }

    public function _after(FunctionalTester $I)
    {
        // remove dataset row created by the first test
        $I->deleteRowByCriteria('dataset',['identifier' =>'346345']);
    }

    /**
     * Test that dataset can be created without custom image, and therefore use the generic image
     * @param FunctionalTester $I
     * @return void
     */
    public function tryToCreateDatasetWithoutCustomImage(FunctionalTester $I)
    {
        //Login as admin
        $I->amOnPage("/site/login");
        $I->submitForm('form.form-horizontal',[
            'LoginForm[username]' => 'admin@gigadb.org',
            'LoginForm[password]' => 'gigadb']
        );
        $I->canSee("Admin");

        //create dataset
        $I->click("Admin");
        $I->click("Datasets");
        $I->click("Create Dataset");
        $I->fillField('#Dataset_identifier', 346345);
        $I->fillField('#Dataset_ftp_site', "ftp://location");
        $I->fillField('#Dataset_dataset_size', 7896);
        $I->fillField('#Dataset_title', "Abracadabra");
        $I->click("Create");
        $I->canSee("10.5524/346345");

        //Ensure there the dataset is now linked to the generic image
        $I->seeInDatabase('dataset',['identifier' => 346345,
            'image_id' => 0]);
    }

    /**
     * Test that the ajax call triggered by "Remove image" button does what it's supposed to do
     * @param FunctionalTester $I
     * @return void
     */
    public function tryToRemoveCustomImageWithAJAX(FunctionalTester $I)
    {
        //Login as admin
        $I->amOnPage("/site/login");
        $I->submitForm('form.form-horizontal',[
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb']
        );

        //Remove custom image for dataset of id 8
        $I->amOnPage("adminDataset/update/id/8");
        $I->click("Remove image");
        $I->sendAjaxPostRequest("/adminDataset/removeImage/", ["doi" => "100006" ]); //make the ajax call the button' javascript would have made
        $I->canSeeInSource('{"status":true}');
        // Ensure dataset of id 8 is now linked to the generic image
        $I->seeInDatabase("dataset", ["id" => 8, "image_id" => 0]);

        // Ensure image record is deleted and that the url of old image is saved in the images_todelete table
        $I->dontSeeInDatabase("image", ["id" => 8]);
        $I->seeInDatabase("images_todelete", [
            "url" => "https://assets.gigadb-cdn.net/live/images/datasets/images/data/cropped/100006_Pygoscelis_adeliae.jpg"
        ]);
    }

    /**
     * Test that image metafields are updated
     *
     * @param FunctionalTester $I
     *
     * @return void
     */
    public function tryToUpdateImageMetafields(FunctionalTester $I)
    {
        //Login as admin
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );
        $I->canSee('Admin');

        //Remove custom image for dataset of id 8
        $I->amOnPage('adminDataset/update/id/8');

        // Upload a new one
        $I->fillField('Image[source]', 'modified');
        $I->fillField('Image[license]', 'modified');
        $I->fillField('Image[photographer]', 'modified');
        $I->click('Save');

        $I->seeInDatabase(
            'image',
            ['id' => 8, 'license' => 'modified', 'photographer' => 'modified', 'source' => 'modified']
        );
    }
}
