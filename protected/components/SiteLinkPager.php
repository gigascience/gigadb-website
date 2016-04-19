<?php

Yii::import("system.web.widgets.pagers.CLinkPager");

class SiteLinkPager extends CLinkPager {
    public $cssFile = '/css/pager.css';
    public function init() {
        parent::init();
        $this->firstPageLabel = Yii::t('app', '<< First');
        $this->footer = Yii::t('app', '');
        $this->header = Yii::t('app', '');
        $this->lastPageLabel = Yii::t('app', 'Last >>');
        $this->nextPageLabel = Yii::t('app', 'Next >');
        $this->prevPageLabel = Yii::t('app', '< Previous');
    }
}