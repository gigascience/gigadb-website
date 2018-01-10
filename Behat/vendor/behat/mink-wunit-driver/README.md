Mink WUnit Driver
=================

[![Latest Stable Version](https://poser.pugx.org/behat/mink-wunit-driver/v/stable.png)](https://packagist.org/packages/behat/mink-wunit-driver)
[![Total Downloads](https://poser.pugx.org/behat/mink-wunit-driver/downloads.png)](https://packagist.org/packages/behat/mink-wunit-driver)

Usage Example
-------------

``` php
<?php

use Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\Driver\WUnitDriver;

$mink = new Mink(array(
    'wunit' => new Session(new WUnitDriver()),
));

$mink->getSession('wunit')->getPage()->findLink('Chat')->click();
```

Behat configuration
-------------------

``` yml
default:
  extensions:
    Behat\MinkExtension\Extension:
      default_session: wunit

    Behat\YiiExtension\Extension:
      mink_driver: true
      framework_script: ../../framework/yii.php
      config_script: ../config/test.php
```

Installation
------------

``` json
{
    "requires": {
        "behat/mink":              "~1.5",
        "behat/mink-extension":    "*",
        "behat/yii-extension":     "*",
        "behat/mink-wunit-driver": "*"
    }
}
```

``` bash
curl http://getcomposer.org/installer | php
php composer.phar install
```

Copyright
---------

Copyright (c) 2012 Patrick Dreyer (patrick.dreyer.name). See LICENSE for details.

Maintainers
-----------

* Patrick Dreyer [patrickdreyer](http://github.com/patrickdreyer)
