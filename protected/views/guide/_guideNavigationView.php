<nav style="margin-bottom: 5px;" aria-label="Submission Guidelines">
    <div style="display:inline-block;">
        <ul class="nav nav-tabs nav-border-tabs" style="margin-top: 1px; margin-bottom: 1px">
            <li class="<?= $isActiveGeneral ? 'active' : '' ?>"><?= CHtml::link('General Submission Guidelines', '/site/guide') ?></li>
            <li class="dropdown<?= !$isActiveGeneral ? ' active' : '' ?>">
            <?= CHtml::htmlButton('Datasets Checklists&nbsp;' . CHtml::tag('i', ['class' => 'fa fa-angle-down'], ''), [
                'class' => 'dropdown-toggle',
                'aria-haspopup' => 'true',
                'aria-expanded' => 'false',
                'aria-label' => 'Dataset Checklists',
                'data-toggle' => 'dropdown',
            ]) ?>
                <ul class="dropdown-menu" style="margin-top: 5px;">
                    <?= $menuHtml ?>
                </ul>
            </li>
        </ul>
    </div>
</nav>
