<?php ?>
<div class="content files-upload">
    <div id="gigadb-fuw">
        <article class="container">
        <?php
        $this->widget('TitleBreadcrumb', [
          'pageTitle' => 'GigaDB: Uploading files for the dataset ' . $identifier,
          'breadcrumbItems' => [
            ['label' => 'Home', 'href' => '/'],
            ['label' => 'Your profile', 'href' => '/user/view_profile#submitted'],
            ['isActive' => true, 'label' => 'Step 1/2: Upload files'],
          ],
        ]);
        ?>
            <div class="row">
                <section class="col-xs-12 col-md-8" aria-label="file uploader">
                    <file-uploader identifier="<?= $identifier ?>" endpoint="<?php echo $tusd_path ?>" />
                </section>
                <div  class="col-xs-12 col-md-4">
                  <aside>
                      <div class="panel tips-panel">
                          <div class="panel-heading">
                              <h4 class="panel-title">Tips</h4>
                          </div>
                          <div class="panel-body">
                              <ul>
                                  <li>This is the first step for submitting files associated to the dataset.</li>
                                  <li>You can upload files from your computer by dragging them to the area on the left.</li>
                                  <li>Once uploads are marked as "Complete", the "Next" button at the bottom will be enabled. You can click it to proceed to the next stage (annotating the files).</li>
                              </ul>
                              <p>For more information about expected files and their formats for given types of datasets, please see the following links:</p>
                              <ul>
                                  <li><a target="_blank" href="http://gigadb.org/site/guidegenomic">Genomic & Transcriptomic datasets</a></li>
                                  <li><a target="_blank" href="http://gigadb.org/site/guideepigenomic">Epigenomic datasets</a></li>
                                  <li><a target="_blank" href="http://gigadb.org/site/guidemetagenomic">Metagenomic datasets</a></li>
                                  <li><a target="_blank" href="http://gigadb.org/site/guideimaging">Imaging datasets</a></li>
                                  <li><a target="_blank" href="http://gigadb.org/site/guidemetabolomic">Metabolomic & Proteomic datasets</a></li>
                                  <li><a target="_blank" href="http://gigadb.org/site/guidesoftware">Software datasets</a></li>
                              </ul>
                          </div>
                      </div>
                  </aside>
                  <file-uploader-next-link
                      identifier="<?= $identifier ?>"
                      uploads-exist="<?= $uploadsCount ?>"
                  />
                </div>
            </div>
        </article>
    </div>
</div>
