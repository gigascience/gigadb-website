<?php

/**
 * CustomGridView Class
 *
 * Extends CGridView to add custom pagination styles by using bootstrap classes.
 *
 * Example usage:
 * ```php
 * $this->widget('CustomGridView', array(
 *   'dataProvider' => $dataProvider,
 *   // other options here...
 * ));
 * ```
 *
 * Note: This class assumes jQuery is loaded. It uses AJAX to refresh the pagination state.
 */


Yii::import('zii.widgets.grid.CGridView');

class CustomGridView extends CGridView {

    public function init() {
        $this->pager = array_merge(
            $this->pager,
            array(
                'header' => '',
                'htmlOptions' => array('class' => 'pagination'),
            )
        );

        $this->pagerCssClass = 'pagination-container';

        parent::init();

        Yii::app()->clientScript->registerScript('pagination-adjustment', '
          if (typeof jQuery !== "undefined") {
            function adjustPagination() {
              $(".pagination > li > a").removeClass("first-visible");
              $(".pagination > li > a").removeClass("last-visible");
              $(".pagination > li:not(.hidden)").first().children("a").addClass("first-visible");
              $(".pagination > li:not(.hidden)").last().children("a").addClass("last-visible");
            }

            adjustPagination();

            // run every time pagination triggers
            $(document).ajaxComplete(function() {
              adjustPagination();
            });
          }
        ', CClientScript::POS_END);
    }
}
