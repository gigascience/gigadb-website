<tr class="odd js-my-item-<?= $exLink->external_link_type_id ?>">
    <td><?= \yii\helpers\Html::encode($exLink->url) ?></td>
    <td><?= $exLink->description ?></td>
    <td>
        <?= $exLink->getTypeName() ?>
    </td>
    <td class="button-column">
        <input type="hidden" class="js-type" value="<?= $exLink->external_link_type_id ?>">
        <input type="hidden" class="js-my-id" value="<?= $exLink->id ?>">
        <a class="js-delete-exLink delete-title" exLink-id="<?=$exLink->id?>" data-id="<?= $model->id ?>" title="delete this row">
            <img alt="delete this row" src="/images/delete.png">
        </a>
    </td>
</tr>