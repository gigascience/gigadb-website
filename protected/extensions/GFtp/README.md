# GFtp

GFtp is a FTP extension for [YII Framework](http://www.yiiframework.com).

It contains :

* `GFtpComponent` : A component used to manage FTP connection and navigation (encapsulates PHP ftp method).
* `GFtpApplicationComponent` : An application component that wraps FTP component.
* `GFtpWidget` : A widget which can be used to display FTP folder content and allow FTP server browsing.

## Installation

As many YII extension, you have to unzip archive under a subfolder of your extensions folder.

__**Subfolder must be named GFtp.**__

## Usage

Here is a basic usage of GFtp extension. More samples could be found on [GFtp extension website](http://yii.guenot.info/index.php?r=site/ftp). 

* Create an FTP application component

```php
return array(
	// [...]
	'components'=>array(
		// [...]
		'ftp' => array(
			'class' => 'ext.GFtp.GFtpApplicationComponent',
			'connectionString' => 'ftp://user:pass@host:21',
			'timeout' => 120,
			'passive' => false
		)
	),
	// [...]
);
```

* Create a local FTP component

```php
Yii::import('ext.GFtp.GFtpComponent')

$gftp = Yii::createComponent('ext.GFtp.GFtpComponent', array(
	'connectionString' => 'ftp://user:pass@host:21', 
	'timeout' => 120, 
	'passive' => false)
);

```

* Use component

```php
$files = $gftp->ls();
$gftp->chdir('images');
```


