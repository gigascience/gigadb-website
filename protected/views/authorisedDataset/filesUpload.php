<?php ?>
<div class="content">
    <div class="container">
        <section class="page-title-section">
            <div id="gigadb-fuw">
                <div class="page-title">
                    <ol class="breadcrumb pull-right">
                        <li><a href="/">Home</a></li>
                        <li class="active">File Upload Wizard</li>
                    </ol>
                    <dataset-info identifier="<?= $identifier ?>" />
                </div>
            </div>
        </section>
        <section>
            <form id="dataset-metadata-form">
                <input id="dataset" type="hidden" v-bind:value="doi">
            </form>
            <div id="drag-drop-area"></div>
            <script>
            document.addEventListener("DOMContentLoaded", function(event) {

                var uppy = Uppy.Core()
                    .use(Uppy.Dashboard, {
                        inline: true,
                        target: '#drag-drop-area',
                        hideAfterFinish: true,
                        showProgressDetails: true,
                        hideUploadButton: false,
                        hideRetryButton: false,
                        hidePauseResumeButton: false,
                        hideCancelButton: false,
                        proudlyDisplayPoweredByUppy: false,
                        metaFields: [
                            { id: 'name', name: 'Name', placeholder: 'file name' },
                            { id: 'description', name: 'Description', placeholder: 'describe what the file is about' }
                        ],
                        locale: {}
                    })
                    .use(Uppy.Form, {
                        target: "#dataset-metadata-form",
                        getMetaFromForm: true,
                        addResultToForm: false,
                        triggerUploadOnSubmit: false,
                        submitOnSuccess: false
                    })
                    // .use(Uppy.Tus, {endpoint: 'https://master.tus.io/files/'});
                    .use(Uppy.Tus, { endpoint: '/files/' });

                uppy.on('complete', function(result) {
                    console.log('Upload complete! We’ve uploaded these files:', result.successful);
                });

            });
            </script>
        </section>
    </div>
</div>