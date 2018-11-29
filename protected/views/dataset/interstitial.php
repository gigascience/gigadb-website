<div class="row">
    <h2>Redirect notice</h2>
    You are about to be redirected to correct location for the data you are looking for.
    Please note the correct reference for these data are:
    <div class="center">
        <p>
            <h4>
            <?= $model->authorNames ?>
            (<?=substr($model->publication_date,0,4)?>): <?= $model->title.' '.$model->publisher->name.'. '; ?>
            <a href="http://dx.doi.org/10.5524/<?= $model->identifier; ?>">http://dx.doi.org/10.5524/<?= $model->identifier ?></a>
            </h4>
        </p>
    </div>
    You will be redirected in 5 seconds. If your browser doesn't redirect you, please click the DOI link above.
</div>
<div class="clear"></div>

<div class="center">
    <a href="/" class="btn-green">GigaDB home</a>
</div>
