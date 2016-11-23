var x = require('casper').selectXPath;

casper.test.begin('Creating a new redirect', 10, function(test) {

    //login
    casper.start("http://127.0.0.1:9170/dataset/admin", function() {
        test.assertExists('form[action="/site/login"]', "main form is found");
        this.fill('form[action="/site/login"]', {
            'LoginForm[username]': "admin@gigadb.org",
            'LoginForm[password]': "gigadb"
        }, true);
    });


    casper.wait(5000, function() {
        this.capture('after_login.png', {
            top: 0,
            left: 0,
            width: 900,
            height: 900
        });
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
        test.assertField('urltoredirect', '');
        this.fill('form[action="/dataset/update/id/210"]', {
            'urltoredirect': "http://foobar.com",
        }, true);

    });


    //tear-down

    casper.then(function() {
         this.click(x('//a[@href="/site/admin"]'));
    });

    casper.waitForUrl("http://127.0.0.1:9170/site/admin", function() {
        test.pass();
    });

    casper.then(function() {
         this.click(x('//a[@href="/dataset/admin"]'));
    });

    casper.waitForUrl("http://127.0.0.1:9170/dataset/admin", function() {
        test.pass();
    });

    casper.then(function() {
         this.click(x('//a[@href="/dataset/update/id/210"]'));
    });

    casper.waitForUrl("http://127.0.0.1:9170/dataset/update/id/210", function() {

        test.assertTitle("GigaDB - Update Dataset", "GigaDB - Update Dataset title is ok");
        this.fill('form[action="/dataset/update/id/210"]', {
            'urltoredirect': '',
        }, true);

    });

    casper.wait(5000, function() {
        this.capture('dataset_update.png', {
            top: 0,
            left: 0,
            width: 900,
            height: 900
        });
    });
    casper.waitForUrl('http://127.0.0.1:9170/dataset/100002', function() {
        test.assertTitle("GigaDB Dataset - DOI 10.5524/100002 - Genomic data from Adelie penguin (Pygoscelis adeline)..", "GigaDB Dataset view Dataset title is ok");
    });

    casper.then(function() {
		 this.click(x('//a[@href="/site/logout"]'));
	});

    casper.waitForUrl('http://127.0.0.1:9170/', function() {
        test.assertTitle("GigaDB", "GigaDB homepage title is ok");
    });


    casper.run(function() {
        test.done();
    });


});
