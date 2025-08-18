<div class='container'>
    <?php
    $this->widget('TitleBreadcrumb', [
        'pageTitle'       => 'View ExternalLinkType #' . $model->id,
        'breadcrumbItems' => [
            ['label' => 'Admin', 'href' => '/site/admin'],
            ['label' => 'Manage', 'href' => '/adminExternalLinkType/admin'],
            ['isActive' => true, 'label' => 'View'],
        ]
    ]);

    $this->widget('zii.widgets.CDetailView', array(
        'data'         => $model,
        'attributes'   => array(
            'name',
            'description',
            'prefix',
            'displayed_as',
            array(
                'name' => 'multiple',
                'value' => function($data) {
                    return $data->multiple? 'Yes' : 'No';
                },
            ),
        ),
        'htmlOptions'  => array('class' => 'table table-striped table-bordered dataset-view-table'),
        'itemCssClass' => array('odd', 'even'),
        'itemTemplate' => '<tr class="{class}"><th scope="row">{label}</th><td>{value}</td></tr>'
    )); ?>
</div>
