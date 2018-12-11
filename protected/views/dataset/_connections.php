 <h5><strong><?= Yii::t('app' , 'Related datasets:')?></strong></h5>
<p>
  <?php foreach ($relations as $relation) { ?>
  doi:<a href="/dataset/<?= $relation['dataset_doi'] ?>"><?= $relation['full_dataset_doi'] ?></a> <?= $relation['relationship'] ?> doi:<a href="/dataset/<?= $relation['related_doi'] ?>"><?=  $relation['full_related_doi']?></a><?= $relation['extra_html'] ?>
  <br>
  <?php } ?>
</p>