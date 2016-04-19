
<h1>Manage Relations</h1>

<a href="/adminRelation/create" class="btn">Create A New Relation</a>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'relation-grid',
    'dataProvider'=>$model->search(),
    'itemsCssClass'=>'table table-bordered',
    'filter'=>$model,
    'columns'=>array(
        array('name'=> 'doi_search', 'value'=>'$data->dataset->identifier'),
        'related_doi',
        array('name'=> 'relationship_name', 'value'=>'$data->relationship->name'),
        array(
            'class'=>'CButtonColumn',
        ),
    ),
)); ?>