<?php

Yii::import("system.web.widgets.pagers.CLinkPager");

class SiteLinkPager extends CLinkPager
{
    public $cssFile = ''; // Added styles to global CSS file
    public function init()
    {
        parent::init();
        $this->firstPageLabel = Yii::t('app', '<< First');
        $this->footer = Yii::t('app', '');
        $this->header = Yii::t('app', '');
        $this->lastPageLabel = Yii::t('app', 'Last >>');
        $this->nextPageLabel = Yii::t('app', 'Next >');
        $this->prevPageLabel = Yii::t('app', '< Previous');
    }

    protected function createPageUrl($page)
    {
        $params = $_GET;

        // hack to prevent multiple pagination params when using two paginations in the same page. Because this is the only use case of this component, this is safe
        unset($params['Samples_page']);
        unset($params['Files_page']);

        $params[$this->pages->pageVar] = $page + 1;

        return $this->getController()->createUrl($this->getController()->getRoute(), $params);
    }
}
