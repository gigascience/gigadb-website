<?php
?>
<div class="content files-annotate">
  <div id="gigadb-fuw">
      <?php
        $alertClasses = '';
        $alertMessage;
        if (Yii::app()->user->hasFlash('filesAnnotate')) {
            $alertClasses = 'alert alert-success';
            $alertMessage = Yii::app()->user->getFlash('filesAnnotate');
        } elseif (Yii::app()->user->hasFlash('filesAnnotateErrors')) {
            $alertClasses = 'alert alert-danger';
            $alertMessage = 'Error: ' . Yii::app()->user->getFlash('filesAnnotateErrors');
        }
        ?>
      <div class="<?php echo $alertClasses ?>" role="alert">
        <?php echo $alertMessage ?>
      </div>
        <div class="container">
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
            <div class="row">
              <div class="col-xs-12">
                <file-annotator
                  identifier="<?php echo $identifier ?>"
                  :uploads='<?php echo json_encode($uploads) ?>'
                  :filetypes='<?php echo $filetypes ?>'
                  :available-attributes='<?php echo $availableAttributes ?>'
                  :attributes='<?php echo json_encode($attributes, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'
                />
              </div>
            </div>
        </d>
    </div>
</div>