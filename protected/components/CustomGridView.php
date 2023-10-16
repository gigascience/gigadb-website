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
 *   CustomGridView::getDefaultActionButtonsConfig()
 * ));
 * ```
 *
 * Note: This class assumes jQuery is loaded. It uses AJAX to refresh the pagination state.
 */


Yii::import('zii.widgets.grid.CGridView');

class CustomGridView extends CGridView
{

  public static function getDefaultActionButtonsConfig()
  {
    return array(
      'class' => 'CButtonColumn',
      'header' => "Actions",
      'headerHtmlOptions' => array('style' => 'width: 100px'),
      'template' => '{view}{update}{delete}',
      'buttons' => array(
        'view' => array(
          'imageUrl' => false,
          'label' => '',
          'options' => array(
            "title" => "View",
            "class" => "fa fa-eye fa-lg icon icon-view",
            "aria-label" => "View"
          ),
        ),
        'update' => array(
          'imageUrl' => false,
          'label' => '',
          'options' => array(
            "title" => "Update",
            "class" => "fa fa-pencil fa-lg icon icon-update",
            "aria-label" => "Update"
          ),
        ),
        'delete' => array(
          'imageUrl' => false,
          'label' => '',
          'options' => array(
            "title" => "Delete",
            "class" => "fa fa-trash fa-lg icon icon-delete",
            "aria-label" => "Delete"
          ),
        ),
      ),
    );
  }

  public function init()
  {
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
