<section>

	<table class="table table-bordered saved-table text-center">
		<thead>
			<tr>
            	<th colspan="9"><?= Yii::t('app', 'Your Saved Search') ?></th>
        	</tr>
			<tr>
				<th>
					Keyword
				</th>
				<th>
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

</section>