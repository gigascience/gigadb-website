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
                <annotator identifier="<?= $identifier ?>" uploads="<?= $uploads ?>" />
            </section>
            <footer>
                <pager identifier="<?= $identifier ?>" />
            </footer>
        </article>
    </div>
</div>