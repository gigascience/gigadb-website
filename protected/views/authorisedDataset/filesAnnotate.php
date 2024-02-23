<?php
?>
<div class="content files-annotate">
    <div id="gigadb-fuw">
        <article class="container">
            <?php
            if (Yii::app()->user->hasFlash('filesAnnotate') || Yii::app()->user->hasFlash('filesAnnotateErrors')) {
                ?>
              <aside class="card" style="padding-top:0.5em">
                  <?php if (Yii::app()->user->hasFlash('filesAnnotate')) { ?>
                      <div class="alert alert-success" role="alert">
                          <?php echo Yii::app()->user->getFlash('filesAnnotate'); ?>
                      </div>
                  <?php } ?>
                  <?php if (Yii::app()->user->hasFlash('filesAnnotateErrors')) { ?>
                      <div class="alert alert-danger" role="alert">
                          <?php echo Yii::app()->user->getFlash('filesAnnotateErrors'); ?>
                      </div>
                  <?php } ?>
              </aside>
                <?php
            }
            ?>
            <?php
            $this->widget('TitleBreadcrumb', [
              'pageTitle' => 'GigaDB: Uploading files for the dataset ' . $identifier,
              'breadcrumbItems' => [
                ['label' => 'Home', 'href' => '/'],
                ['label' => 'Your profile', 'href' => '/user/view_profile#submitted'],
                ['label' => 'Step 1/2: Upload files', 'href' => '/authorisedDataset/uploadFiles/id/' . $identifier],
                ['isActive' => true, 'label' => 'Step 2/2: Annotate files'],
              ],
            ]);
            ?>
            <?php echo CHtml::beginForm(); ?>
                <section class="row">
                    <file-annotator identifier="<?php echo $identifier ?>"
                        :uploads='<?php echo json_encode($uploads) ?>'
                        :filetypes='<?php echo $filetypes ?>'
                        :attributes='<?php echo json_encode($attributes, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'
                    />
                </section>
                <footer>
                    <page-navigation identifier="<?php echo $identifier; ?>" />
                </footer>
            <?php echo CHtml::endForm(); ?>
        </article>
    </div>
</div>