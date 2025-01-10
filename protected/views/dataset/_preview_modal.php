<div id="<?php echo $modalId; ?>" class="modal fade preview-modal" tabindex="-1" role="dialog"
  aria-labelledby="<?php echo $modalId; ?>Title">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close modal-close-btn" data-dismiss="modal"
          aria-label="Close Preview">&times;</button>
        <div class="modal-title-container">
          <h3 class="h4 modal-title" id="<?php echo $modalId; ?>Title">Preview</h3>
          <a id="<?php echo $modalId; ?>Link" href="" target="_blank" rel="noopener noreferrer"
            aria-label="Open in new tab"><span class="fa fa-external-link" aria-hidden="true"></span></a>
        </div>
      </div>
      <div class="modal-body preview-modal-body">
        <p id="<?php echo $modalId; ?>Description"></p>
        <div id="<?php echo $modalId; ?>Content" class="preview-modal-content"></div>
      </div>
    </div>
  </div>
</div>

<script>
  const createPreviewModal = (modalId) => {
    const selectors = {
      modal: `#${modalId}`,
      content: `#${modalId}Content`,
      title: `#${modalId}Title`,
      link: `#${modalId}Link`,
      description: `#${modalId}Description`
    };

    const elements = {
      modal: $(selectors.modal),
      content: $(selectors.content),
      title: $(selectors.title),
      link: $(selectors.link),
      description: $(selectors.description)
    };

    const createLoadingSpinner = () => (
      $('<div>').addClass('loading-spinner')
        .append($('<i>').addClass('fa fa-spinner fa-spin fa-3x').attr('aria-hidden', 'true'))
        .append($('<p>').text('Loading preview...'))
    );

    const createErrorAlert = (message) => (
      $('<div>').addClass('alert alert-danger')
        .append($('<p>').text(message))
    );

    const loadImagePreview = (fileData) => {
      const img = new Image();

      img.onload = () => {
        const imageElement = $('<img>').attr({
          src: fileData.location,
          alt: fileData.name,
          class: 'preview-image'
        });
        elements.content.html(imageElement);
      };
      img.onerror = () => {
        elements.content.html(createErrorAlert('Error loading image'));
      };
      img.src = fileData.location;
    };

    const loadIframePreview = (fileData) => {
      const iframe = $('<iframe>')
        .attr({
          src: fileData.location,
          class: 'preview-iframe',
          title: `Preview of ${fileData.name}`
        })
        .on('load', function () {
          $(this).show();
          $(this).siblings('.loading-spinner').remove();
        })
        .hide();

      const iframeContainer = $('<div>')
        .addClass('preview-iframe-container')
        .append(iframe)
        .append(createLoadingSpinner());

      elements.content.html(iframeContainer);
    };

    const showUnsupportedFormatMessage = () => {
      elements.content.html(
        $('<p>').addClass('alert alert-gigadb-info')
          .text('Preview not available for this file type')
      );
    };

    const updateModalContent = (fileData) => {
      elements.modal.removeAttr('inert aria-hidden');
      elements.title.text(`Preview of ${fileData.name}`);
      elements.link.attr('href', fileData.location);
      elements.description.text(fileData.description);
      elements.content.html(createLoadingSpinner());

      if (fileData.type === 'Image') {
        loadImagePreview(fileData);
      } else if (['TEXT', 'HTML', 'PDF'].includes(fileData.format)) {
        loadIframePreview(fileData);
      } else {
        showUnsupportedFormatMessage();
      }
    };

    const handleModalShow = (event) => {
      const buttonElement = $(event.relatedTarget);
      const fileData = {
        location: buttonElement.data('file-location'),
        name: buttonElement.data('file-name'),
        type: buttonElement.data('file-type'),
        format: buttonElement.data('file-format'),
        description: buttonElement.data('file-description') || ''
      };

      updateModalContent(fileData);
    };

    const handleModalHide = () => {
      elements.modal.attr({
        'inert': true,
        'aria-hidden': true
      });
      elements.content.empty();
    };

    elements.modal.on('show.bs.modal', handleModalShow);
    elements.modal.on('hidden.bs.modal', handleModalHide);
  };

  createPreviewModal('<?php echo $modalId; ?>');
</script>