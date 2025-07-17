<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/main',
     * meaning using a single column layout. See 'protected/views/layouts/main.php'.
     */
    public $layout = '//layouts/main';
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();

        /**
         * All metadata we need for SEO
         * @var array
         */
        public $metaData = array(
            'title' => '',
            'description' => '',
            'private' => false,
            'redirect' => false,
        );

    public $loadBaBbqPolyfills = false;

    /*
     * An Admin has role == 'admin', that stored in the user obj
     *
     */
        public function isAdmin()
        {
            if (!isset(Yii::app()->user->role)) {
                return false;
            }
            if (Yii::app()->user->role === 'admin') {
                return true;
            }
            return false;
        }
    /*
     * An admin has all roles of a registered user
     *
     */

        public function isUser()
        {
            if (!isset(Yii::app()->user->role)) {
                return false;
            }
            if (Yii::app()->user->role === 'user') {
                return true;
            }
            return $this->isAdmin();
        }

    /**
     * Everything in init will be run before any action from controllers inheriting from this class.
     * It is needed so we can reconnect to the database server automatically when connection was terminated
     * So that users don't get shown a server error (usually anytime after nightly backup to S3)
     *
     * @return void
     * @throws CException
     */
        public function init(): void {
            parent::init();
            try {
                Yii::app()->db->createCommand('select null;')->execute();
            } catch (CDbException $e) {
                Yii::app()->db->setActive(false);
                Yii::app()->db->setActive(true);
            }
        }
}
