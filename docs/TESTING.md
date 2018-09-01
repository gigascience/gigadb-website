# Running automated tests for GigaDB web site

## Acceptance tests

#### Preparing

Install Behat and PHPUnit with composer

```bash
$ cd Behat
$ composer install
```

Install PhantomJS

using NPM:
```bash
$ npm install phantomjs
```

Or using Brew (macOS):

```bash
$ brew install phantomjs
```

Run PhantomJS as WebDriver compatible browser:

```bash
$ phantomjs --webdriver=8643
```

Set up the environment variables for test users

```bash
$ . test_users.txt
```

#### Running the tests

```bash
$ ./tests/run

```


The test runner will restore the state of the main database after running all the tests.
It will (re)create the test database needed by Yii database fixtures and will run Behat acceptance tests and PHPUNit unit tests.

#### Assisting feature development 

###### 1. Write feature and scenarios (acceptance tests)

* See example in (tests/features/affiliate-login.feature)
* Guideline here:  (http://docs.behat.org/en/v2.5/quick_intro.html#define-your-feature)

###### 2. Create or reuse step definitions (a.k.a functional tests)

* See: (tests/features/boostrap/FeatureContext.php)


###### 3. Run the tests and implement the functionality

use can use tags (e.g: @wip, @todo, @ok, @broken,...) to run a subset of tests and stop the suite upon failure with ``--stop-on-failure``.


```bash
$ Behat/bin/behat --tags @ok,@wip -v --stop-on-failure -c tests/behat.yml

```

**Note:** Some tags have special meaning: @javascript force the scenario to run using a javascript-enabled headless browser (phantomjs or whatever WebDriver API is running on port 8643). If @javascript is not present PHP BrowserKit-based GoutteDriver is used (it is faster) (the @mink:goutte enabling this is optional as it's default behaviour)

###### 4. Write unit tests for PHP functions

The test runner will run all unit tests. but can also be run by themselves:

(After logging into vagrant with ``vagrant ssh``)

```bash
$ cd /vagrant/protected/tests
$ ./../../Behat/vendor/phpunit/phpunit/phpunit --config=phpunit.xml unit

```

To generate a test coverage report, run the following commands in vagrant:

```bash
$ cd /vagrant/protected/tests
$ ./../../Behat/vendor/phpunit/phpunit/phpunit --config=phpunit.xml --coverage-html ./report  unit

```

The unit tests use PHPUnit 4.1.* (any more recent version will fail to work with Yii 1.1) and the configuration files are:
* ``protected/tests/phpunit.xml``
* ``protected/tests/bootstrap.php``
* ``protected/config/test.php``
* ``protected/config/db_test.json``

They make use of database test fixtures which need to be dropped and recreated at every run so they use a separate ``gigadb_test`` database, so not to lose data from the main database.


## Functional tests with CasperJS


Those are legacy tests that use CasperJS. They will eventually be rewritten using Behat.

#### Preparation

Install CasperJS and its dependency PhantomJS

using NPM:

```bash
$ npm install phantomjs
$ npm install casperjs
```

On macOS, brew can be used too:

```bash
$ brew install casperjs
```

##### running the tests

```bash
$ casperjs test tests/*.js
```


