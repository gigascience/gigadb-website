casper.test.begin('Managing keywords on dataset admin form', 16, function suite(test) {
    casper.start("http://127.0.0.1:9170/site/login", function() {
        test.assertTitle("GigaDB - Login", "web page title ok");
        test.assertExists('form[action="/site/login"]', "main form is found");
        this.fill('form[action="/site/login"]', {
            'LoginForm[username]': "admin@gigadb.org",
			'LoginForm[password]': "gigadb"
        }, true);
    });

    casper.thenOpen('http://127.0.0.1:9170/dataset/update/id/210', function() {
        this.fill('form[action="/dataset/update/id/210"]', {
            'keywords': "abcd, a four part keyword, my_keyword, my-keyword, my dodgy tag<script>alert('xss!');</script>",
        }, true);
    });

    casper.thenOpen('http://127.0.0.1:9170/dataset/100002', function () {
        test.assertTextExist('Keywords:','Keywords label is shown on dataset view');
        test.assertTextExist('Keywords:','Keywords label is shown on dataset view');
        test.assertTextExist('abcd','Keywords are shown on dataset view');
        test.assertTextExist('a four part keyword','Keywords are shown on dataset view');
        test.assertTextExist('my_keyword','Keywords are shown on dataset view');
        test.assertTextExist('my-keyword','Keywords are shown on dataset view');
    });

    casper.waitForAlert(function fail (response) {
        //this.echo("Alert received: " + response.data);
        test.fail('dangerous entries are not properly filtered');
    }, function success (response) {
        test.pass('keywords are filtered properly');
    });

    casper.thenOpen('http://127.0.0.1:9170/dataset/update/id/210', function() {
        this.fill('form[action="/dataset/update/id/210"]', {
            'keywords': "abcd, my_keyword, my-keyword, new tag",
        }, true);
    });

    casper.thenOpen('http://127.0.0.1:9170/dataset/100002', function () {
        test.assertTextExist('Keywords:','Keywords label is shown on dataset view');
        test.assertTextExist('abcd','Keywords are shown on dataset view');
        test.assertTextDoesntExist('a four part keyword','Removed keywords are not shown on dataset view');
        test.assertTextExist('my_keyword','Keywords are shown on dataset view');
        test.assertTextExist('my-keyword','Keywords are shown on dataset view');
        test.assertTextExist('new tag','New keyword is shown on dataset view');
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
