<?php
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
echo $menuHtml;
echo CHtml::closeTag('ul');

echo CHtml::closeTag('li');
echo CHtml::closeTag('ul');
echo CHtml::closeTag('div');
echo CHtml::closeTag('nav');
