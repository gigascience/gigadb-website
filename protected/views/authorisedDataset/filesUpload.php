<?php ?>
<div class="content">
    <div id="gigadb-fuw">
        <div class="container">
            <section class="page-title-section">
                <div class="page-title">
                    <ol class="breadcrumb pull-right">
                        <li><a href="/">Home</a></li>
                        <li class="active">File Upload Wizard</li>
                    </ol>
                    <dataset-info identifier="<?= $identifier ?>" />
                </div>
            </section>
            <section>
                <uploader identifier="<?= $identifier ?>"
                            endpoint="/files/" />
            </section>
        </div>
    </div>
</div>