<?php 
	$from = ($page-1) * $limit + 1;
	$to = $page*$limit; 
?>
<p>
    <?php if($from <= $total_dataset) { ?>
    Showing <strong><?= $from?> - <?= $to > $total_dataset ? $total_dataset: $to ?> of <?= $total_dataset ?></strong> datasets
    <?php } ?>
</p>