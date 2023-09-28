<?php

/**
 * TitleBreadcrumb Widget
 *
 * This component is used to render a breadcrumb and a page title.
 *
 * Usage Example:
 *
 * ```php
 * $this->widget('application.components.TitleBreadcrumb', [
 *     'pageTitle' => 'Your Page Title',
 *     'breadcrumbItems' => [
 *         ['isActive' => true, 'label' => 'Admin'],
 *         ['label' => 'Dashboard', 'href' => '/dashboard']
 *         // Add more items as needed
 *     ]
 * ]);
 * ```
 *
 * `pageTitle`: string
 * The main title displayed on the page. Required.
 *
 * `breadcrumbItems`: array
 * An array of breadcrumb items. Each item is an associative array with keys:
 * - `isActive`: bool, optional. Whether the breadcrumb item is active. Will NOT render a link if true.
 * - `label`: string, required. The display text of the breadcrumb item.
 * - `href`: string, optional. The URL / pathname for the breadcrumb item.
 */


/**
 * Class TitleBreadcrumb
 * Renders a breadcrumb and a page title.
 */
class TitleBreadcrumb extends CWidget
{
  public $pageTitle;
  public $breadcrumbItems = [];
  const ACTIVE_CLASS = 'active';

  private function generateBreadcrumbItems(): string
  {
    return implode("\n", array_map(function ($item) {
      $isActive = $item['isActive'] ?? false;
      $label = $item['label'];
      $href = $item['href'] ?? '#';

      if ($isActive) {
        return CHtml::tag('li', ['class' => self::ACTIVE_CLASS], $label);
      }

      return CHtml::tag('li', [], CHtml::link($label, $href));
    }, $this->breadcrumbItems));
  }

  public function run()
  {
    if (empty($this->pageTitle)) {
      throw new CException('pageTitle must be set.');
    }

    $breadcrumbHtml = $this->generateBreadcrumbItems();

    Yii::app()->controller->renderPartial('//shared/_titleBreadcrumb', [
      'pageTitle' => $this->pageTitle,
      'breadcrumbHtml' => $breadcrumbHtml
    ]);
  }
}
