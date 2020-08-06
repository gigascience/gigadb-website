<?php
Yii::import("system.web.widgets.pagers.CListPager");

class SiteListPager extends CListPager
{
    public function init()
    {
        parent::init();
        $this->header = Yii::t('app', '');
    }
}


