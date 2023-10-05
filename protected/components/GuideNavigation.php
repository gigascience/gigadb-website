<?php
class GuideNavigation extends CWidget {

    private function isActive($controllerName, $actionName) {
        $controller = Yii::app()->controller->id;
        $action = Yii::app()->controller->action->id;
        return ($controller === $controllerName && $action === $actionName);
    }

    private function generateMenuItems(array $datasetLinks): string {
        return implode("\n", array_map(function($label, $url) {
            return CHtml::tag('li', [], CHtml::link($label, $url));
        }, array_keys($datasetLinks), $datasetLinks));
    }

    public function run() {
        $isActiveGeneral = $this->isActive('site', 'guide');

        $datasetLinks = [
            'Genomic Dataset Checklist' => 'guidegenomic',
            'Imaging Dataset Checklist' => 'guideimaging',
            'Metabolomic and Lipidomic Dataset Checklist' => 'guidemetabolomic',
            'Epigenomic Dataset Checklist' => 'guideepigenomic',
            'Metagenomic Dataset Checklist' => 'guidemetagenomic',
            'Software Dataset Checklist' => 'guidesoftware'
        ];

        $menuHtml = $this->generateMenuItems($datasetLinks);

        Yii::app()->controller->renderPartial('//guide/_guideNavigationView', [
            'menuHtml' => $menuHtml,
            'isActiveGeneral' => $isActiveGeneral
        ]);
    }
}
?>
