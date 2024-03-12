<div class="container dataset-submission-page">
  <?php
  $this->widget('TitleBreadcrumb', [
    'pageTitle' => 'Add Authors',
    'breadcrumbItems' => []
  ]);

  ?>

  <?
  $this->renderPartial('_nav', array('model' => $model));
  ?>

  <form class="form well js-author-amangement-form" novalidate dataset-id="<?= $model->id ?>">
    <div class="form-horizontal">
      <div id="author-grid" class="author-grid grid-view">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th id="author-grid_c0" class="first-name-col">
                <label for="js-author-first-name">First name<span> *</span></label>
              </th>
              <th id="author-grid_c1" class="middle-name-col">
                <label for="js-author-middle-name">
                  Middle name
                </label>
              </th>
              <th id="author-grid_c2" class="last-name-col">
                <label for="js-author-last-name">
                  Last name<span> *</span>
                </label>
              </th>
              <th id="author-grid_c3" class="author-orcid-col">
                <div data-toggle="tooltip" data-html="true" tabindex="0"
                  title="ORCID provides a persistent digital identifier that distinguishes you from every other researcher.  Please visit <a class='tooltip-link' tabindex='-1' href='http://orcid.org/'>http://orcid.org/</a> to learn more.">
                  <label for="js-author-orcid">ORCiD</label>
                  <i class="fa fa-question-circle" aria-hidden="true"></i>
                </div>
              </th>
              <th id="author-grid_c4" class="order-col">
                <div data-toggle="tooltip"
                  title="This is the order in which authors will appear in the dataset citation." tabindex="0">
                  <span>Order</span>
                  <i class="fa fa-question-circle" aria-hidden="true"></i>
                </div>
              </th>
              <th id="author-grid_c5" class="button-column">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($das) { ?>
              <?php foreach ($das as $da) {
                $authorFullName = $da->author->first_name . ' ' . $da->author->middle_name . ' ' . $da->author->surname;
                ?>
                <tr class="odd">
                  <td>
                    <?= $da->author->first_name ?>
                  </td>
                  <td>
                    <?= $da->author->middle_name ?>
                  </td>
                  <td>
                    <?= $da->author->surname ?>
                  </td>
                  <td>
                    <?= $da->author->orcid ?>
                  </td>
                  <td>
                    <input type="number" class='js-author-rank form-control' id="js-author-rank-<?= $da->id ?>"
                      da-id="<?= $da->id ?>" aria-label="Order for author <?= $authorFullName ?>"
                      aria-describedby="js-author-rank-<?= $da->id ?>-desc" />
                    <span class="sr-only" id="js-author-rank-<?= $da->id ?>-desc">This is the order in which authors will
                      appear in the dataset citation</span>
                  </td>
                  <td class="button-column">
                    <button data-toggle="tooltip" title="Delete author <?php echo $authorFullName ?>"
                      class="delete-author-btn js-delete-author fa fa-trash fa-lg icon icon-delete" da-id="<?= $da->id ?>"
                      aria-label="Delete author <?php echo $authorFullName ?>"></button>
                  </td>
                </tr>
              <? } ?>
            <? } else { ?>
              <tr>
                <td colspan="4">
                  <span class="empty">No results found.</span>
                </td>
              </tr>
              <tr>
              <? } ?>
              <td>
                <input id="js-author-first-name" class="form-control" type="text" name="Author[first_name]"
                  placeholder="First Name" aria-required="true" required>
              </td>
              <td>
                <input id="js-author-middle-name" class="form-control" type="text" name="Author[middle_name]"
                  placeholder="Middle Name">
              </td>
              <td>
                <input id="js-author-last-name" class="form-control" type="text" name="Author[last_name]"
                  placeholder="Last Name" aria-required="true" required>
              </td>
              <td>
                <input id="js-author-orcid" class="form-control" type="text" name="Author[orcid]" placeholder="ORCiD"
                  aria-describedby="author-orcid-desc">
                <span class="sr-only" id="author-orcid-desc">ORCID provides a persistent digital identifier that
                  distinguishes you from every other researcher. Please visit http://orcid.org/ to learn more.</span>
              </td>
              <td></td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="add-author-container btns-row btns-row-end">
        <button dataset-id="<?= $model->id ?>" class="btn background-btn-o js-add-author" type="submit">
          Add Author
        </button>
      </div>
    </div>

  </form>
  <div class="btns-row btns-row-end">
    <a href="/datasetSubmission/datasetManagement/id/<?= $model->id ?>" class="btn background-btn">Previous</a>
    <a href="/user/view_profile" title="Save your incomplete submission and leave the submission wizard."
      class="btn background-btn delete-title">Save & Quit</a>
    <a href="/datasetSubmission/projectManagement/id/<?= $model->id ?>" class="btn background-btn">Next</a>
  </div>
</div>

<script>
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })

  $(document).ready(function () {
    $('.js-author-amangement-form').on('submit', function (e) {
      e.preventDefault()

      const form = event.target

      if (!form.checkValidity()) {
        form.reportValidity()
        return
      }
      addAuthor(this)


    })
  })

  function ajaxIndicatorStart(text) {
    if ($('body').find('#resultLoading').attr('id') != 'resultLoading') {
      $('body').append('<div id="resultLoading" style="display:none"><div><img width="30" src="/images/ajax-loader.gif"><div>' + text + '</div></div><div class="bg"></div></div>');
    }

    $('#resultLoading').css({
      'width': '100%',
      'height': '100%',
      'position': 'fixed',
      'z-index': '10000000',
      'top': '0',
      'left': '0',
      'right': '0',
      'bottom': '0',
      'margin': 'auto'
    });

    $('#resultLoading .bg').css({
      'background': '#000000',
      'opacity': '0.7',
      'width': '100%',
      'height': '100%',
      'position': 'absolute',
      'top': '0'
    });

    $('#resultLoading>div:first').css({
      'width': '250px',
      'height': '75px',
      'text-align': 'center',
      'position': 'fixed',
      'top': '0',
      'left': '0',
      'right': '0',
      'bottom': '0',
      'margin': 'auto',
      'font-size': '16px',
      'z-index': '10',
      'color': '#ffffff'

    });

    $('#resultLoading .bg').height('100%');
    $('#resultLoading').fadeIn(300);
    $('body').css('cursor', 'wait');
  }

  function ajaxIndicatorStop() {
    $('#resultLoading .bg').height('100%');
    $('#resultLoading').fadeOut(300);
    $('body').css('cursor', 'default');
  }

  $(".js-open-rank").click(function (e) {
    e.preventDefault();
    var daid = $(this).attr('da-id');
    $('.js-author-rank').hide();
    $("#js-author-rank-" + daid).show();
    $('.js-open-rank').show();
    $(this).hide();
  });

  $(".js-author-rank").change(function (e) {
    e.preventDefault();
    var daid = $(this).attr('da-id');
    var rank = $(this).val();
    $.ajax({
      type: 'POST',
      url: '/adminDatasetAuthor/updateRank',
      data: { 'da_id': daid, 'rank': rank },
      beforeSend: function () {
        ajaxIndicatorStart('loading data.. please wait..');
      },
      success: function (response) {
        if (response.success == true) {
          window.location.reload();
        } else {
          alert(response.message);
        }
      },
      error: function () {
      }
    });
  });

  function addAuthor(el) {
    var did = $(el).attr('dataset-id');
    var first_name = $('#js-author-first-name').val();
    var last_name = $('#js-author-last-name').val();
    var middle_name = $('#js-author-middle-name').val();
    var orcid = $('#js-author-orcid').val();

    var author = {
      'first_name': first_name,
      'last_name': last_name,
      'middle_name': middle_name,
      'orcid': orcid,
    }

    $.ajax({
      type: 'POST',
      url: '/adminDatasetAuthor/addAuthor',
      data: { 'dataset_id': did, 'Author': author },
      success: function (response) {
        if (response.success) {
          window.location.reload();
        } else {
          alert(response.message);
        }
      },
      error: function () {
      }
    });
  }

  $(".js-delete-author").click(function (e) {
    if (!confirm('Are you sure you want to delete this item?'))
      return false;
    e.preventDefault();
    var daid = $(this).attr('da-id');

    $.ajax({
      type: 'POST',
      url: '/adminDatasetAuthor/deleteAuthor',
      data: { 'da_id': daid },
      beforeSend: function () {
        ajaxIndicatorStart('loading data.. please wait..');
      },
      success: function (response) {
        if (response.success) {
          window.location.reload();
        } else {
          alert(response.message);
        }
      },
      error: function () {
      }
    });
  })

  $(document).ajaxStop(function () {
    //hide ajax indicator
    ajaxIndicatorStop();
  });
</script>