var x = require('casper').selectXPath;

casper.test.begin('Navigating to a dataset admin page', 4, function(test) {

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
	});

	casper.run(function() {
		test.done();
	});

});
