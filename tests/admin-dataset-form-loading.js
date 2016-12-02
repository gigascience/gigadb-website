var x = require('casper').selectXPath;

casper.test.begin('Navigating to a dataset admin page', 28, function(test) {

	casper.start("http://127.0.0.1:9170/dataset/admin", function() {
        test.assertExists('form[action="/site/login"]', "main form is found");
        this.fill('form[action="/site/login"]', {
            'LoginForm[username]': "admin@gigadb.org",
			'LoginForm[password]': "gigadb"
        }, true);
    });

	casper.waitForUrl('http://127.0.0.1:9170/dataset/admin', function() {
		test.assertTitle("GigaDB - Admin Dataset", "GigaDB - Admin Dataset is ok");
		test.assertExists(x('//a[@href="/dataset/update/id/210"]'));
	});

    casper.then(function() {
		 this.click(x('//a[@href="/dataset/update/id/210"]'));
	});

	casper.waitForUrl('http://127.0.0.1:9170/dataset/update/id/210', function() {
		test.assertTitle("GigaDB - Update Dataset", "GigaDB - Update Dataset title is ok");
        test.assertSelectorHasText('h2','Update Dataset 100002');
        test.assertExists(x('//select[@id="Dataset_submitter_id"]'), 'Submitter drop-down menu');
        test.assertExists(x('//select[@id="Dataset_upload_status"]'), 'Upload status drop-down menu');
        test.assertElementCount(x('//input[@type="checkbox" and contains(@name,"datasettypes")]'),10);
        test.assertExists(x('//input[@type="text" and @id="Dataset_title"]'), 'Dataset Title');
        test.assertExists(x('//textarea[@id="Dataset_description"]'), 'Dataset Description');
        test.assertExists(x('//input[@type="text" and @size="60" and @maxlength="200" and @id="Dataset_dataset_size"]'), 'Dataset Size');
        test.assertExists(x('//input[@type="file" and @id="image_upload_image"]'), 'Image upload control');
        test.assertExists(x('//input[@type="text" and @size="60" and @maxlength="200" and @id="Images_url"]'), 'Image Url');
        test.assertExists(x('//input[@type="text" and @size="60" and @maxlength="200" and @id="Images_source"]'), 'Image Source');
        test.assertExists(x('//input[@type="text" and @size="60" and @maxlength="200" and @id="Images_tag"]'), 'Image Tag');
        test.assertExists(x('//input[@type="text" and @size="60" and @maxlength="200" and @id="Images_license"]'), 'Image License');
        test.assertExists(x('//input[@type="text" and @size="60" and @maxlength="200" and @id="Images_photographer"]'), 'Image Photographer');
        test.assertExists(x('//input[@type="text" and @size="32" and @maxlength="32" and @disabled="disabled" and @id="Dataset_identifier"]'), 'Dataset Identifier');
		test.assertExists(x('//input[contains(@class, "btn-green") and @value="Mint DOI"]'),'Mint DOI button')
		test.assertExists(x('//input[@type="text" and @size="60" and @maxlength="200" and @disabled="disabled" and @id="Dataset_ftp_site"]'), 'FTP site');
        test.assertExists(x('//select[@id="Dataset_publisher_id"]'), 'Publisher drop-down menu');
        test.assertExists(x('//input[@type="text" and @id="Dataset_fairnuse"]'), 'Dataset Fair User Policy');
        test.assertExists(x('//input[@type="text" and @disabled="disabled" and @id="Dataset_publication_date"]'), 'Publication Date');
        test.assertExists(x('//input[@type="text" and @id="Dataset_modification_date"]'), 'Modification Date');
        test.assertSelectorHasText('a.btn', 'Cancel');
        test.assertSelectorHasText(x('//input[@type="submit" and @onclick="js:checkdate()" and @class="btn-green"]'), 'Save');
		test.assertExists(x('//input[@type="text" and @id="keywords"]'), 'Keywords');
		test.assertExists(x('//input[@type="text" and @id="urltoredirect"]'), 'Cited URL to redirect');
	});



	casper.run(function() {
		test.done();
	});

});
