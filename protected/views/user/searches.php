<div class="tab-pane active">
	<table class="table table border">
		<thead>
			<tr>
            	<th colspan="9"><?= Yii::t('app', 'Your Saved Search') ?></th>
        	</tr>
			<tr>
				<th class="span3">
					Keyword
				</th>
				<th class="span4">
					Result
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($searchRecord as $sr) { ?>
				<tr>
					<td><?= $sr->name ?></td>
					<?/*
					<td><?= $sr->resultDetail ?></td>
					*/?>
					<td>
						<a href="<?= $sr->searchPage ?>" class="btn">View Result</a>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>