<?php 
?>
<div class="content">
    <div id="gigadb-fuw">
        <article class="container">
            <aside class="card" style="padding-top:0.5em">
                <?php if (Yii::app()->user->hasFlash('filesAnnotate')) { ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo Yii::app()->user->getFlash('filesAnnotate'); ?>
                    </div>
                <? } ?>
                <?php if (Yii::app()->user->hasFlash('filesAnnotateErrors')) { ?>
                    <div class="alert alert-error" role="alert">
                        <?php echo Yii::app()->user->getFlash('filesAnnotateErrors'); ?>
                    </div>
                <? } ?>
            </aside>
            <?php echo CHtml::beginForm(); ?>
                <header class="page-title-section">
                    <div class="page-title">
                        <ol class="breadcrumb pull-right">
                            <li><a href="/">Home</a></li>
                            <li><a href="/user/view_profile#submitted">Your profile</a></li>
                            <li><a href="/authorisedDataset/uploadFiles/id/<?php echo $identifier; ?>">Step 1/2: Upload files</a></li>
                            <li class="active">Step 2/2: Annotate files</li>
                        </ol>
                        <dataset-info identifier="<?php echo $identifier; ?>" />
                    </div>
                </header>
                <section class="row">
                    <annotator identifier="<?php echo $identifier ?>" 
                                v-bind:uploads='<?php echo json_encode($uploads) ?>' 
                                v-bind:attributes='<?php echo json_encode($attributes, JSON_HEX_APOS|JSON_HEX_QUOT) ?>' 
                    />
                </section>
                <footer>
                    <pager identifier="<?php echo $identifier; ?>" />
                </footer>
            <?php echo CHtml::endForm(); ?>
        </article>
    </div>
</div>