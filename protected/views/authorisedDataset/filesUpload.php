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
                    <dataset-info identifier="<?php echo $identifier ?>" />
                </div>
            </header>
            <section>
                <annotator identifier="<?php echo $identifier ?>" v-bind:uploads="<?php echo json_encode($uploads) ?>" />
            </section>
            <footer>
                <pager identifier="<?php echo $identifier ?>" />
            </footer>
        </article>
    </div>
</div>