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

        echo CHtml::openTag('nav', ['style' => 'margin-bottom: 5px;', 'aria-label' => 'Submission Guidelines']);
        echo CHtml::openTag('div', ['style' => 'display:inline-block;']);
        echo CHtml::openTag('ul', ['class' => 'nav nav-tabs nav-border-tabs', 'style' => 'margin-top: 1px; margin-bottom: 1px']);

        echo CHtml::tag('li', ['class' => $isActiveGeneral ? 'active' : ''], CHtml::link('General Submission Guidelines', '/site/guide'));

        echo CHtml::openTag('li', ['class' => 'dropdown' . (!$isActiveGeneral ? ' active' : '')]);
        echo CHtml::link('Datasets Checklists&nbsp;' . CHtml::tag('i', ['class' => 'fa fa-angle-down'], ''), '#', [
            'class' => 'dropdown-toggle',
            'aria-haspopup' => 'true',
            'aria-expanded' => 'false',
            'aria-label' => 'Dataset Checklists',
            'data-toggle' => 'dropdown',
        ]);

        echo CHtml::openTag('ul', ['class' => 'dropdown-menu', 'style' => 'margin-top: 5px;']);

        $datasetLinks = [
            'Genomic Dataset Checklist' => 'guidegenomic',
            'Imaging Dataset Checklist' => 'guideimaging',
            'Metabolomic and Lipidomic Dataset Checklist' => 'guidemetabolomic',
            'Epigenomic Dataset Checklist' => 'guideepigenomic',
            'Metagenomic Dataset Checklist' => 'guidemetagenomic',
            'Software Dataset Checklist' => 'guidesoftware'
        ];

        echo $this->generateMenuItems($datasetLinks);
        echo CHtml::closeTag('ul');

        echo CHtml::closeTag('li');
        echo CHtml::closeTag('ul');
        echo CHtml::closeTag('div');
        echo CHtml::closeTag('nav');
    }
}
?>
