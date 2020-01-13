<?php ?>
<div class="content">
    <div id="gigadb-fuw">
        <article class="container">
            <header class="page-title-section">
                <div class="page-title">
                    <ol class="breadcrumb pull-right">
                        <li><a href="/">Home</a></li>
                        <li class="active">File Upload Wizard</li>
                    </ol>
                    <dataset-info identifier="<?= $identifier ?>" />
                </div>
            </header>
            <section>
                <uploader identifier="<?= $identifier ?>"
                            endpoint="/files/"
                />
            </section>
            <footer>
                <pager/>
            </footer>
        </article>
    </div>
</div>