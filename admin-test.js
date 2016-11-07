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


casper.test.begin('Navigation to the dataset wizard', 8, function(test) {

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

	casper.then(function() {

		this.test.assertExists('#agree-checkbox1');
		this.click('#agree-checkbox1');
		this.wait(100, function() {
		    this.test.assert(this.evaluate(function () {
		      return document.getElementById('agree-checkbox1').checked;
		  }), "terms agreement check-box is checked");
		});

	});

	// this.evaluate(function() {
	//     $('input[type="submit"]:first').click();
	// });
	casper.then(function() {
		 this.click(x('//input[@value="Submission wizard"]'));
	});

	casper.waitForUrl('http://127.0.0.1:9170/dataset/create1', function() {
		test.assertTitle("GigaDB - Create1 Dataset", "GigaDB - Create1 Dataset title is ok");
	});

	casper.run(function() {
		test.done();
	});

});
