<div class="container">
    <?php
    $this->widget('TitleBreadcrumb', [
        'pageTitle' => 'View File #' . $model->id,
        'breadcrumbItems' => [
            ['label' => 'Admin', 'href' => '/site/admin'],
            ['label' => 'Manage', 'href' => '/adminFile/admin'],
            ['isActive' => true, 'label' => 'View'],
        ]
    ]);
    ?>

    <?php
    $sample_id = FileSample::model()->find('file_id=:file_id', array(':file_id' => $model->id));
    $fileAttributes = FileAttributes::model()->findAll('file_id=:file_id', array(':file_id' => $model->id));

    if (isset($sample_id)) {
        $sample_name = Sample::model()->find('id=:id', array(':id' => $sample_id->sample_id));
    }

    $name = "Not Set";

    if (isset($sample_id) && isset($sample_name)) {
        $name = $sample_name->name;
    }

    $attributes = array(
        'id',
        'dataset_id',
        'name',
        'location',
        'extension',
        'size',
        'description',
        'date_stamp',
        'format_id',
        'type_id',
        array(
            'name' => 'Sample',
            'value' => $name,
        )
    );

    if (!empty($fileAttributes)) {
        foreach ($fileAttributes as $fileAttribute) {
            array_push($attributes, array('name' => 'FileAttribute', 'value' => $fileAttribute->value));
        }
    }

    $this->widget('zii.widgets.CDetailView', array(
        'data' => $model,
        'attributes' => $attributes,
        'htmlOptions' => array('class' => 'table table-striped table-bordered dataset-view-table'),
        'itemCssClass' => array('odd', 'even'),
        'itemTemplate' => '<tr class="{class}"><th scope="row">{label}</th><td>{value}</td></tr>'

    ));

    ?>
</div>