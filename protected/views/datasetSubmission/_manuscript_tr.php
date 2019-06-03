<tr class="odd js-my-item-<?= AIHelper::MANUSCRIPTS ?>">
    <td><?= \yii\helpers\Html::encode($manuscript->identifier) ?></td>
    <td></td>
    <td>manuscript</td>
    <td class="button-column">
        <input type="hidden" class="js-type" value="<?= AIHelper::MANUSCRIPTS ?>">
        <input type="hidden" class="js-my-id" value="<?= $manuscript->id ?>">
        <a class="js-delete-exLink delete-title" exLink-id="<?=$manuscript->id?>" data-id="<?= $model->id ?>" title="delete this row">
            <img alt="delete this row" src="/images/delete.png">
        </a>
    </td>
</tr>