<?php 
?>
<div class="content">
    <div id="gigadb-fuw">
        <article class="container">
            <?php echo CHtml::beginForm(); ?>
                <header class="page-title-section">
                    <div class="page-title">
                        <ol class="breadcrumb pull-right">
                            <li><a href="/">Home</a></li>
                            <li class="active">File Upload Wizard</li>
                        </ol>
                        <dataset-info identifier="<?php echo $identifier; ?>" />
                    </div>
                </header>
                <section>
                    <annotator identifier="<?php echo $identifier; ?>" 
                                v-bind:uploads='<?php echo json_encode($uploads); ?>' 
                                v-bind:attributes='<?php echo json_encode($attributes); ?>' 
                    />
                </section>
                <footer>
                    <pager identifier="<?php echo $identifier; ?>" />
                </footer>
            <?php echo CHtml::endForm(); ?>
        </article>
    </div>
</div>