var x = require('casper').selectXPath;
var random =  Math.floor(Math.random() * 10) ;

casper.test.begin('Creating a new redirect', 4, function(test) {

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
        this.fill('form[action="/dataset/update/id/210"]', {
            'urltoredirect': "http://127.0.0.1:9170/dataset/100002/token/" + random,
        }, true);

    });


    casper.run(function() {
        test.done();
    });


});

casper.test.begin('Navigating to the meta refresh interstitial', 2, function(test) {

    casper.start("http://127.0.0.1:9170/dataset/100002/token/" + random, function() {
        // casper.wait(1000, function() {
        //     this.capture('interstitial.png', {
        //         top: 0,
        //         left: 0,
        //         width: 900,
        //         height: 900
        //     });
        // });
        console.log("http://127.0.0.1:9170/dataset/100002/token/" + random);
        test.assertTextExists('Redirect notice', "interstitial text is found");
    });

    // logout
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
