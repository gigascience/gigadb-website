<?php
namespace backend\tests;

use \League\Flysystem\Adapter\Local;
use \Yii;
use \yii\base\InvalidConfigException;
use \League\Flysystem\Filesystem as NativeFilesystem;

/**
 * TestFilesystem
 *
 * This is a support class to help us test worker classes that need to interact with Flysystem
 * It will help us inject Mocks into Flysystem multiple layers of abstraction
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class TestFilesystem extends \creocoder\flysystem\Filesystem
{
    /**
     * @var string
     */
    public $path;

    /**
     * @var Local
     */
    public $adapter;

    public $nativeFilesystem;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->path === null) {
            throw new InvalidConfigException('The "path" property must be set.');
        }

        $this->path = Yii::getAlias($this->path);

        parent::init();
        // $this->filesystem = new NativeFilesystem($this->adapter, $this->config);
        $this->filesystem = $this->nativeFilesystem;
    }

    /**
     * @return Local
     */
    protected function prepareAdapter()
    {
        return $this->adapter;
    }
}
