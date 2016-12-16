var x = require('casper').selectXPath;

casper.test.begin('Mint A DOI from dataset admin form', 8, function(test) {

    //login
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
        test.assertExists(x('//a[contains(@class, "btn-green") and text()="Mint DOI"]'),'Mint DOI button')

    });

    casper.then(function() {
         this.click(x('//a[text()="Mint DOI"]'));

    });

    casper.then(function() {
        test.assertTextExists('minting under way, please wait','button pressed acknowledgment text');
    });

    casper.waitForSelectorTextChange('#minting', function() {
        test.assertTextExists('new DOI successfully minted','new DOI minted');
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
