# Running automated tests for GigaDB web site

## Acceptance tests

#### Preparation

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
$ . test_users
```

#### Running the tests

```bash
$ Behat/bin/behat  -c Behat/behat.yml

```

#### Assisting feature development 

###### 1. Write feature and scenarios (acceptance tests)

* See example in (tests/features/affiliate-login.feature)
* Guideline here:  (http://docs.behat.org/en/v2.5/quick_intro.html#define-your-feature)

###### 2. Create or reuse step definitions (a.k.a functional tests)

* See: (tests/features/boostrap/FeatureContext.php)


###### 3. Run the tests and implement the functionality

use can use tags (e.g: @wip, @todo, @ok, @broken,...) to run a subset of tests and stop the suite upon failure with ``--stop-on-failure``.


```bash
$ Behat/bin/behat --tags @ok,@wip -v --stop-on-failure -c Behat/behat.yml

```

**Note:** Some tags have special meaning: @javascript force the scenario to run using a javascript-enabled headless browser (phantomjs or whatever WebDriver API is running on port 8643). If @javascript is not present PHP BrowserKit-based GoutteDriver is used (it is faster) (the @mink:goutte enabling this is optional as it's default behaviour)


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


