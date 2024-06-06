<?php

class AdminDatasetImageCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * Test that uploading a new image after removing previous one doesn't alter the DB record for the generic image
     *
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function tryToRemoveCustomImageThenUploadNewOne(AcceptanceTester $I)
    {
        //Login as admin
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );
        $I->canSee('Admin');
        $originalImageId = $I->grabFromDatabase('dataset', 'image_id', ['id' => 8]);

        //Remove custom image for dataset of id 8
        $I->amOnPage('adminDataset/update/id/8');
        $I->click('Remove image');
        $I->acceptPopup();
        $I->wait(3);

        $temporaryImageId = $I->grabFromDatabase('dataset', 'image_id', ['id' => 8]);

        $I->assertEquals(0, $temporaryImageId);

        // Upload a new one
        $I->attachFile('#datasetImage', 'bgi_logo_new.png');
        $I->fillField('Image[source]', 'funky testy');
        $I->fillField('Image[license]', 'CC0');
        $I->fillField('Image[photographer]', 'Honky Tonky');
        $I->click('Save');

        // Ensure the database record for the generic image is not altered
        $I->seeInDatabase('image', [
            'id'           => 0,
            'url'          => 'https://assets.gigadb-cdn.net/images/datasets/no_image.png',
            'license'      => 'All rights reserved',
            'photographer' => 'n/a',
            'source'       => 'GigaDB'
        ]);

        $imageUrl = $I->grabFromDatabase('dataset', 'image_id', ['id' => 8]);
        $I->assertNotEquals($temporaryImageId, $imageUrl);
        $I->assertNotEquals($originalImageId, $imageUrl);
    }

    /**
     * Test that default image is set if one removes the previous image without uploading a new one and doesn't alter the DB record for the generic image
     *
     * @param AcceptanceTester $I
     *
     * @return void
     */
    public function tryToRemoveCustomImageWithoutUploadingANewOne(AcceptanceTester $I)
    {
        //Login as admin
        $I->amOnPage('/site/login');
        $I->submitForm('form.form-horizontal', [
                'LoginForm[username]' => 'admin@gigadb.org',
                'LoginForm[password]' => 'gigadb'
            ]
        );
        $I->canSee('Admin');

        $originalImageId = $I->grabFromDatabase('dataset', 'image_id', ['id' => 8]);

        //Remove custom image for dataset of id 8
        $I->amOnPage('adminDataset/update/id/8');
        $I->click('#clearFileUrl');
        $I->acceptPopup();
        $I->wait(3);

        $imageId = $I->grabFromDatabase('dataset', 'image_id', ['id' => 8]);
        $I->assertEquals($originalImageId, $imageId);

        $imageUrl = $I->grabFromDatabase('image', 'url', ['id' => 8]);
        $I->assertEquals(null, $imageUrl);

        $I->fillField('Image[source]', 'funky testy');
        $I->fillField('Image[license]', 'CC0');
        $I->fillField('Image[photographer]', 'Honky Tonky');
        $I->click('Save');
        $I->waitForElementVisible('#badge-div');


        $postImageId = $I->grabFromDatabase('dataset', 'image_id', ['id' => 8]);
        $I->assertNotEquals($originalImageId, $postImageId);
        $I->assertEquals(0, $postImageId);

        // check it's not updated
        $I->seeInDatabase('image', [
            'id'           => 0,
            'url'          => 'https://assets.gigadb-cdn.net/images/datasets/no_image.png',
            'license'      => 'All rights reserved',
            'tag'          => null,
            'location'     => 'no_image.png',
            'source'       => 'GigaDB'
        ]);
    }
}
