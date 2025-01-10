<div id="<?php echo $modalId; ?>" class="modal fade preview-modal" tabindex="-1" role="dialog"
  aria-labelledby="<?php echo $modalId; ?>Label" inert>
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close modal-close-btn" data-dismiss="modal" aria-label="Close Preview">&times;</button>
        <div class="modal-title-container">
          <h3 class="h4 modal-title" id="<?php echo $modalId; ?>Label">Preview</h3>
          <a id="<?php echo $modalId; ?>Link" href="" target="_blank" aria-label="Open file in new tab"><span
              class="fa fa-external-link" aria-hidden="true"></span></a>
        </div>
      </div>
      <div class="modal-body">
        <p id="<?php echo $modalId; ?>Description"></p>
        <div id="<?php echo $modalId; ?>Content"></div>
      </div>
    </div>
  </div>
</div>

<script>
  const modalId = '#<?php echo $modalId; ?>';
  const modalIdContent = `${modalId}Content`;
  const modalIdLabel = `${modalId}Label`;
  const modalIdLink = `${modalId}Link`;
  const modalIdDescription = `${modalId}Description`;

  $(modalId).on('show.bs.modal', function (event) {
    const buttonElement = $(event.relatedTarget);
    const modalElement = $(modalId);
    const descriptionElement = $(modalIdDescription);

    // file attributes
    const location = buttonElement.data('file-location');
    const name = buttonElement.data('file-name');
    const type = buttonElement.data('file-type');
    const format = buttonElement.data('file-format');
    const description = buttonElement.data('file-description') || '';

    const contentElement = $(modalIdContent);
    const titleElement = $(modalIdLabel);
    const linkElement = $(modalIdLink);

    modalElement.removeAttr('inert');
    modalElement.removeAttr('aria-hidden');
    titleElement.text(`Preview of ${name}`);
    linkElement.attr('href', location);
    descriptionElement.text(description);

    const isImage = type === 'Image';
    const isIframeDisplayable = ['TEXT', 'HTML', 'PDF'].includes(format);

    let contentHtml = '';
    switch (true) {
      case isImage:
        contentHtml = $('<img>')
        .attr({
          'src': location,
          'alt': name,
          'class': 'preview-image'
        });
        break;
      case isIframeDisplayable:
        contentHtml = $('<iframe>')
          .attr({
            'src': location,
            'class': 'preview-iframe'
          });
        break;
      default:
        contentHtml = $('<p>').text('Preview not available for this file type');
    }
    contentElement.html(contentHtml);
  });

  $(modalId).on('hidden.bs.modal', function () {
    const modalElement = $(modalId);
    modalElement.attr('inert', true);
    modalElement.attr('aria-hidden', true);
    $(modalIdContent).empty();
  });
</script>