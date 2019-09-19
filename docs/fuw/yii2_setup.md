

## Install

```
user@host $ cd fuw/
user@host $ composer global require "fxp/composer-asset-plugin:~1.3"
user@host $ composer create-project --prefer-dist yiisoft/yii2-app-advanced app
```
```
Installing yiisoft/yii2-app-advanced (2.0.19)
  - Installing yiisoft/yii2-app-advanced (2.0.19): Downloading (100%)         
Created project in app
Loading composer repositories with package information
Updating dependencies (including require-dev)
Package operations: 70 installs, 0 updates, 0 removals
  - Installing yiisoft/yii2-composer (2.0.7): Downloading (100%)         
  - Installing phpspec/php-diff (v1.1.0): Downloading (100%)         
  - Installing bower-asset/jquery (3.4.1): Downloading (100%)         
  - Installing bower-asset/yii2-pjax (2.0.7.1): Downloading (100%)         
  - Installing bower-asset/punycode (v1.3.2): Downloading (100%)         
  - Installing bower-asset/inputmask (3.3.11): Downloading (100%)         
  - Installing cebe/markdown (1.2.1): Downloading (100%)         
  - Installing ezyang/htmlpurifier (v4.10.0): Downloading (100%)         
  - Installing yiisoft/yii2 (2.0.20): Downloading (100%)         
  - Installing yiisoft/yii2-gii (2.1.0): Downloading (100%)         
  - Installing sebastian/version (2.0.1): Loading from cache
  - Installing sebastian/resource-operations (1.0.0): Downloading (100%)         
  - Installing sebastian/recursion-context (3.0.0): Downloading (100%)         
  - Installing sebastian/object-reflector (1.1.1): Downloading (100%)         
  - Installing sebastian/object-enumerator (3.0.3): Downloading (100%)         
  - Installing sebastian/global-state (2.0.0): Downloading (100%)         
  - Installing sebastian/exporter (3.1.0): Downloading (100%)         
  - Installing sebastian/environment (3.1.0): Downloading (100%)         
  - Installing sebastian/diff (2.0.1): Downloading (100%)         
  - Installing sebastian/comparator (2.1.3): Downloading (100%)         
  - Installing doctrine/instantiator (1.2.0): Downloading (100%)         
  - Installing phpunit/php-text-template (1.2.1): Loading from cache
  - Installing phpunit/phpunit-mock-objects (5.0.10): Downloading (100%)         
  - Installing phpunit/php-timer (1.0.9): Loading from cache
  - Installing phpunit/php-file-iterator (1.4.5): Loading from cache
  - Installing theseer/tokenizer (1.1.2): Downloading (100%)         
  - Installing sebastian/code-unit-reverse-lookup (1.0.1): Downloading (100%)         
  - Installing phpunit/php-token-stream (2.0.2): Downloading (100%)         
  - Installing phpunit/php-code-coverage (5.3.2): Downloading (100%)         
  - Installing symfony/polyfill-ctype (v1.11.0): Downloading (100%)         
  - Installing webmozart/assert (1.4.0): Downloading (100%)         
  - Installing phpdocumentor/reflection-common (1.0.1): Loading from cache
  - Installing phpdocumentor/type-resolver (0.4.0): Loading from cache
  - Installing phpdocumentor/reflection-docblock (4.3.1): Downloading (100%)         
  - Installing phpspec/prophecy (1.8.0): Downloading (100%)         
  - Installing phar-io/version (1.0.1): Loading from cache
  - Installing phar-io/manifest (1.0.1): Downloading (100%)         
  - Installing myclabs/deep-copy (1.9.1): Downloading (100%)         
  - Installing phpunit/phpunit (6.5.14): Downloading (100%)         
  - Installing codeception/verify (1.0.0): Downloading (100%)         
  - Installing bower-asset/bootstrap (v3.4.1): Downloading (100%)         
  - Installing yiisoft/yii2-bootstrap (2.0.10): Downloading (100%)         
  - Installing symfony/polyfill-php72 (v1.11.0): Downloading (100%)         
  - Installing symfony/polyfill-mbstring (v1.11.0): Downloading (100%)         
  - Installing symfony/polyfill-intl-idn (v1.11.0): Downloading (100%)         
  - Installing symfony/polyfill-iconv (v1.11.0): Downloading (100%)         
  - Installing doctrine/lexer (v1.0.1): Downloading (100%)         
  - Installing egulias/email-validator (2.1.8): Downloading (100%)         
  - Installing swiftmailer/swiftmailer (v6.2.1): Downloading (100%)         
  - Installing yiisoft/yii2-swiftmailer (2.1.2): Downloading (100%)         
  - Installing yiisoft/yii2-debug (2.1.5): Downloading (100%)         
  - Installing fzaninotto/faker (v1.8.0): Downloading (100%)         
  - Installing yiisoft/yii2-faker (2.0.4): Downloading (100%)         
  - Installing codeception/phpunit-wrapper (6.6.1): Downloading (100%)         
  - Installing codeception/stub (2.1.0): Downloading (100%)         
  - Installing behat/gherkin (v4.6.0): Downloading (100%)         
  - Installing symfony/dom-crawler (v4.3.1): Downloading (100%)         
  - Installing symfony/css-selector (v4.3.1): Downloading (100%)         
  - Installing symfony/browser-kit (v4.2.4): Downloading (100%)         
  - Installing symfony/yaml (v4.3.1): Downloading (100%)         
  - Installing symfony/event-dispatcher-contracts (v1.1.1): Downloading (100%)         
  - Installing symfony/event-dispatcher (v4.3.1): Downloading (100%)         
  - Installing symfony/service-contracts (v1.1.2): Downloading (100%)         
  - Installing symfony/polyfill-php73 (v1.11.0): Downloading (100%)         
  - Installing symfony/console (v4.3.1): Downloading (100%)         
  - Installing symfony/finder (v4.3.1): Downloading (100%)         
  - Installing ralouphie/getallheaders (2.0.5): Downloading (100%)         
  - Installing psr/http-message (1.0.1): Loading from cache
  - Installing guzzlehttp/psr7 (1.5.2): Downloading (100%)         
  - Installing codeception/base (2.5.6): Downloading (100%)         
sebastian/global-state suggests installing ext-uopz (*)
phpunit/php-code-coverage suggests installing ext-xdebug (^2.5.5)
phpunit/phpunit suggests installing phpunit/php-invoker (^1.1)
phpunit/phpunit suggests installing ext-xdebug (*)
swiftmailer/swiftmailer suggests installing true/punycode (Needed to support internationalized email addresses, if ext-intl is not installed)
symfony/browser-kit suggests installing symfony/process ()
symfony/event-dispatcher-contracts suggests installing psr/event-dispatcher ()
symfony/event-dispatcher suggests installing symfony/dependency-injection ()
symfony/event-dispatcher suggests installing symfony/http-kernel ()
symfony/service-contracts suggests installing psr/container ()
symfony/service-contracts suggests installing symfony/service-implementation ()
symfony/console suggests installing symfony/lock ()
symfony/console suggests installing symfony/process ()
symfony/console suggests installing psr/log (For using the console logger)
codeception/base suggests installing aws/aws-sdk-php (For using AWS Auth in REST module and Queue module)
codeception/base suggests installing codeception/specify (BDD-style code blocks)
codeception/base suggests installing codeception/phpbuiltinserver (Start and stop PHP built-in web server for your tests)
codeception/base suggests installing flow/jsonpath (For using JSONPath in REST module)
codeception/base suggests installing phpseclib/phpseclib (for SFTP option in FTP Module)
codeception/base suggests installing league/factory-muffin (For DataFactory module)
codeception/base suggests installing league/factory-muffin-faker (For Faker support in DataFactory module)
codeception/base suggests installing symfony/phpunit-bridge (For phpunit-bridge support)
codeception/base suggests installing stecman/symfony-console-completion (For BASH autocompletion)
Package phpunit/phpunit-mock-objects is abandoned, you should avoid using it. No replacement was suggested.
Writing lock file
Generating autoload files
```

## Dockerfiles

Update the Dockerfile in app/backend and app/frontend to use php-fpm and to expose a unique port.

Also create a Dockerfile in app/console to use as bastion/test runner.

## Nginx

update nginx config to proxy request to app/backend and app/frontend.

## Docker Compose

Create new services for frontend, backend and console with a build context pointing to app/frontend, app/backend and app/console respectively.

Ensure there is already a database service to be used by the project.

## Initial config

```
user@host $ docker-compose exec console bash
root@0fefc7cefabe:/var/www# php /app/init
Yii Application Initialization Tool v1.0

Which environment do you want the application to be initialized in?

  [0] Development
  [1] Production

  Your choice [0-1, or "q" to quit] 0

  Initialize the application under 'Development' environment? [yes|no] yes

  Start initialization ...

   generate frontend/config/test-local.php
   generate frontend/config/params-local.php
   generate frontend/config/main-local.php
   generate frontend/config/codeception-local.php
   generate frontend/web/index.php
   generate frontend/web/robots.txt
   generate frontend/web/index-test.php
   generate yii
   generate backend/config/test-local.php
   generate backend/config/params-local.php
   generate backend/config/main-local.php
   generate backend/config/codeception-local.php
   generate backend/web/index.php
   generate backend/web/robots.txt
   generate backend/web/index-test.php
   generate common/config/test-local.php
   generate common/config/params-local.php
   generate common/config/main-local.php
   generate common/config/codeception-local.php
   generate yii_test.bat
   generate yii_test
   generate console/config/test-local.php
   generate console/config/params-local.php
   generate console/config/main-local.php
   generate cookie validation key in backend/config/main-local.php
   generate cookie validation key in common/config/codeception-local.php
   generate cookie validation key in frontend/config/main-local.php
      chmod 0777 backend/runtime
      chmod 0777 backend/web/assets
      chmod 0777 console/runtime
      chmod 0777 frontend/runtime
      chmod 0777 frontend/web/assets
      chmod 0755 yii
      chmod 0755 yii_test

  ... initialization completed.
```

## Configuring the app

Ensure main-local.php in ``app/frontend`` and ``app/backend`` are configured with a database.

## Running tests

Ensure test-local.php in ``app/console`` is configured with a test database.

```
user@host $ docker exec console bash
root@5a2b444ed400:/var/www# cd /app
root@5a2b444ed400:/app# php yii_test migrate
root@5a2b444ed400:/app# vendor/bin/codecept build
root@5a2b444ed400:/app# vendor/bin/codecept run
```