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


casper.test.begin('Adding new keywords', 7, function(test) {

	casper.start("http://127.0.0.1:9170/dataset/update/id/210", function() {

        test.assertTitle("GigaDB - Update Dataset", "GigaDB - Update Dataset title is ok");
        this.fill('form[action="/dataset/update/id/210"]', {
            'keywords': "abcd, a four part keyword, my_keyword, my-keyword",
        }, true);

    });

    casper.waitForUrl('http://127.0.0.1:9170/dataset/100002', function() {
        test.assertTitle("GigaDB Dataset - DOI 10.5524/100002 - Genomic data from Adelie penguin (Pygoscelis adeline)..", "GigaDB Dataset view Dataset title is ok");
        test.assertTextExist('Keywords:','Keywords label is shown on dataset view');
        test.assertTextExist('abcd','Keywords are shown on dataset view');
        test.assertTextExist('a four part keyword','Keywords are shown on dataset view');
        test.assertTextExist('my_keyword','Keywords are shown on dataset view');
        test.assertTextExist('my-keyword','Keywords are shown on dataset view');
    });


    casper.wait(5000, function() {
        this.capture('dataset_view.png', {
            top: 0,
            left: 0,
            width: 900,
            height: 900
        });
    });


    casper.run(function() {
        test.done();
    });

});



// Removing some keywords, Adding new keywords
casper.test.begin('Removing some keywords, Adding new keywords', 8, function(test) {

	casper.start("http://127.0.0.1:9170/dataset/update/id/210", function() {

        test.assertTitle("GigaDB - Update Dataset", "GigaDB - Update Dataset title is ok");
        this.fill('form[action="/dataset/update/id/210"]', {
            'keywords': "abcd, my_keyword, my-keyword, new tag",
        }, true);

    });

    casper.waitForUrl('http://127.0.0.1:9170/dataset/100002', function() {
        test.assertTitle("GigaDB Dataset - DOI 10.5524/100002 - Genomic data from Adelie penguin (Pygoscelis adeline)..", "GigaDB Dataset view Dataset title is ok");
        test.assertTextExist('Keywords:','Keywords label is shown on dataset view');
        test.assertTextExist('abcd','Keywords are shown on dataset view');
        test.assertTextDoesntExist('a four part keyword','Removed keywords are not shown on dataset view');
        test.assertTextExist('my_keyword','Keywords are shown on dataset view');
        test.assertTextExist('my-keyword','Keywords are shown on dataset view');
        test.assertTextExist('new tag','New keyword is shown on dataset view');
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
// trying xss
casper.test.begin('Keywords are filtered against xss', 8, function(test) {

	casper.start("http://127.0.0.1:9170/dataset/update/id/210", function() {

        test.assertTitle("GigaDB - Update Dataset", "GigaDB - Update Dataset title is ok");
        this.fill('form[action="/dataset/update/id/210"]', {
            'keywords': "abcd, my_keyword, my-keyword, my dodgy tag<script>alert('xss!');</script>",
        }, true);

    });

    casper.waitForUrl('http://127.0.0.1:9170/dataset/100002', function() {
        test.assertTitle("GigaDB Dataset - DOI 10.5524/100002 - Genomic data from Adelie penguin (Pygoscelis adeline)..", "GigaDB Dataset view Dataset title is ok");
        test.assertTextExist('Keywords:','Keywords label is shown on dataset view');
        test.assertTextExist('abcd','Keywords are shown on dataset view');
        test.assertTextDoesntExist('a four part keyword','Removed keywords are not shown on dataset view');
        test.assertTextExist('my_keyword','Keywords are shown on dataset view');
        test.assertTextExist('my-keyword','Keywords are shown on dataset view');
    });

    casper.waitForAlert(function fail (response) {
        //this.echo("Alert received: " + response.data);
        test.fail('dangerous entries are not properly filtered');
    }, function success (response) {
        test.pass('keywords are filtered properly');
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
