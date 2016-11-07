var x = require('casper').selectXPath;

casper.test.begin('Navigation to profile page', 5, function(test) {

	casper.start("http://127.0.0.1:9170/site/login", function() {
        test.assertExists('form[action="/site/login"]', "main form is found");
        this.fill('form[action="/site/login"]', {
            'LoginForm[username]': "user@gigadb.org",
			'LoginForm[password]': "gigadb"
        }, true);
    });

	casper.waitForUrl('http://127.0.0.1:9170/', function() {
		test.assertTitle("GigaDB", "GigaDB title is ok");
		test.assertSelectorHasText('a.btn', 'John\'s GigaDB Page');
	});

	casper.then(function() {
		 this.clickLabel('John\'s GigaDB Page', 'a');
	});

	casper.waitForUrl('http://127.0.0.1:9170/user/view_profile', function() {
		test.assertTitle("GigaDB - My GigaDB Page", "GigaDB - My GigaDB Page title is ok");
		test.assertSelectorHasText('a.btn-green', ' Submit new dataset');
	});

	casper.run(function() {
		test.done();
	});

});


casper.test.begin('Submission of a new dataset', 5, function(test) {

	casper.start("http://127.0.0.1:9170/site/login", function() {
        test.assertExists('form[action="/site/login"]', "main form is found");
        this.fill('form[action="/site/login"]', {
            'LoginForm[username]': "user@gigadb.org",
			'LoginForm[password]': "gigadb"
        }, true);
    });

	casper.waitForUrl('http://127.0.0.1:9170/', function() {
		test.assertTitle("GigaDB", "GigaDB title is ok");
		test.assertSelectorHasText('a.btn', 'John\'s GigaDB Page');
	});

	casper.thenOpen('http://127.0.0.1:9170/dataset/upload');

	casper.waitForUrl('http://127.0.0.1:9170/dataset/upload', function() {
		test.assertTitle("GigaDB - Upload Dataset", "GigaDB - Upload Dataset title is ok");
		test.assertSelectorHasText('input[type="submit"]', 'Submission wizard');
	});

	casper.run(function() {
		test.done();
	});

});
