var x = require('casper').selectXPath;

casper.test.begin('Removing all keywords', 6, function(test) {

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
        this.fill('form[action="/dataset/update/id/210"]', {
            'keywords': "",
        }, true);
    });

    // casper.wait(5000, function() {
    //     this.capture('dataset_update.png', {
    //         top: 0,
    //         left: 0,
    //         width: 900,
    //         height: 900
    //     });
    // });

    casper.waitForUrl('http://127.0.0.1:9170/dataset/100002', function() {
        test.assertTitle("GigaDB Dataset - DOI 10.5524/100002 - Genomic data from Adelie penguin (Pygoscelis adeline)..", "GigaDB Dataset view Dataset title is ok");
        test.assertTextDoesntExist('Keywords:','The dataset view should not contain "Keywords:" when no keywords');
    });


    // casper.wait(5000, function() {
    //     this.capture('dataset_view.png', {
    //         top: 0,
    //         left: 0,
    //         width: 900,
    //         height: 900
    //     });
    // });

    casper.run(function() {
		test.done();
	});

});


// Adding new keywords
// 'Keywords': "abcd, a four part keyword, my_keyword, my-keyword",

// Removing some keywords, Adding new keywords

// trying sql injection
