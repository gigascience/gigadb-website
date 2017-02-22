/**
 * Created by rija on 21/02/2017.
 */


var x = require('casper').selectXPath;

casper.test.begin('Navigating directories in the file table', 11, function(test) {
    // test.assert(false,"dataset file table is there with files");
    // test.assert(false,"dataset file table is there with directories");
    // test.assert(false,"navigate to directory");
    // test.assert(false,"files appear in directory listing");
    // test.assert(false,"directories appear in directory listing");
    // test.assert(false,"paginate through the directory listing");
    // test.assert(false,"navigating to sub-directory");
    // test.assert(false,"navigating to parent directory with breadcrumb");
    // test.assert(false,"navigating to dataset files with breadcrumb");
    // test.done();


    //set up cookie to have the page size set to 5 so we can exercise pagination
    phantom.addCookie({
        domain: '127.0.0.1',
        name: 'file_setting',
        value: '%7B%22setting%22%3A%5B%22name%22%2C%22sample_id%22%2C%22type_id%22%2C%22format_id%22%2C%22size%22%2C%22date_stamp%22%2C%22location%22%5D%2C%22page%22%3A%2205%22%7D'
    });

    casper.start('http://127.0.0.1:9170/dataset/view/id/100117');

    // dataset file table is there with directories
    casper.waitForText("AltSplicing", function() {
        test.assertExists(x('//a[@href="/dataset/view/id/100117?location=ftp://climb.genomics.cn/pub/10.5524/100001_101000/100117/AltSplicing#file_table"]'),'Directory type entries have link to ftp table');
    });

    casper.then(function() {
        this.click(x('//ul[@id="file_table_pager"]//a[text()="Next >"]'));

    });



    // dataset file table is there with files
    casper.waitForUrl("http://127.0.0.1:9170/dataset/view/id/100117/File_page/2", function() {
        test.assertExists(x('//a[@href="ftp://climb.genomics.cn/pub/10.5524/100001_101000/100117/contributors.txt"]'),'File type entries have direct link to ftp server');
    });

    // navigating to a directory
    casper.then(function() {
        this.click(x('//ul[@id="file_table_pager"]//a[text()="< Previous"]'));

    });

    casper.then(function() {
        this.click(x('//a[@href="/dataset/view/id/100117?location=ftp://climb.genomics.cn/pub/10.5524/100001_101000/100117/AltSplicing#file_table"]'));

    });

    casper.waitForUrl("http://127.0.0.1:9170/dataset/view/id/100117?location=ftp://climb.genomics.cn/pub/10.5524/100001_101000/100117/AltSplicing#file_table", function() {
        test.assertExists(x('//a[text()="ASClustering.Rmd" and @href="ftp://climb.genomics.cn/pub/10.5524/100001_101000/100117/AltSplicing/ASClustering.Rmd"]'),'ftp table: File type entries have direct link to ftp server');
        test.assertExists(x('//a[text()="GeneByGeneAnalysis" and @href="http://127.0.0.1:9170/dataset/view/id/100117?location=ftp://climb.genomics.cn/pub/10.5524/100001_101000/100117/AltSplicing/GeneByGeneAnalysis#file_table"]'),'ftp table: Directory type entries have  link to ftp table');
    });

    //testing the pagination
    casper.then(function() {
        this.click(x('//ul[@id="ftp_table_pager"]//a[text()="Next >"]'));
    });

    casper.then(function() {
        test.assertExists(x('//a[text()="data" and @href="http://127.0.0.1:9170/dataset/100117?location=ftp://climb.genomics.cn/pub/10.5524/100001_101000/100117/AltSplicing/data#file_table"]'));
        test.assertExists(x('//a[text()="results" and @href="http://127.0.0.1:9170/dataset/100117?location=ftp://climb.genomics.cn/pub/10.5524/100001_101000/100117/AltSplicing/results#file_table"]'));
    });

    //testing the breadcrumbs
    casper.then(function() {
        test.assertTextExists("Dataset files » AltSplicing");
        test.assertExists(x('//a[text()="Dataset files" and @href="/dataset/view/id/100117#file_table"]'));
    });

    casper.then(function() {
        this.click(x('//a[@href="http://127.0.0.1:9170/dataset/100117?location=ftp://climb.genomics.cn/pub/10.5524/100001_101000/100117/AltSplicing/results#file_table"]'));

    });

    casper.then(function() {
        test.assertTextExists("Dataset files » AltSplicing/ » results");
        test.assertExists(x('//a[text()="Dataset files" and @href="/dataset/view/id/100117#file_table"]'));
        test.assertExists(x('//a[text()="AltSplicing/" and @href="http://127.0.0.1:9170/dataset/100117?location=ftp://climb.genomics.cn/pub/10.5524/100001_101000/100117/AltSplicing#file_table"]'));
    });


    casper.wait(1000, function() {
        this.capture('ftp_table.png', {
            top: 0,
            left: 0,
            width: 900,
            height: 3000
        });
    });

    casper.run(function() {
        test.done();
    });

});