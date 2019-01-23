<em>(It is a more recent version of this dataset)</em>

<div class="modal fade" tabindex="-1" role="dialog" data-show="true" id="oldVersionAlert">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">New Version Alert</h4>
      </div>
      <div class="modal-body">
        <p>There is a new version of this dataset available at DOI <?=$relation['full_related_doi']?></p>
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-default" data-dismiss="modal">Close</a>
        <a href="/dataset/<?= $relation['related_doi'] ?>" class="btn btn-default">View new version</a>
        <a href="/dataset/<?= $relation['dataset_doi'] ?>" class="btn btn-default">Continue to view old version</a>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
  document.addEventListener("DOMContentLoaded", function(event) { //This event is fired after deferred scripts are loaded
    $(window).on('load', function(){
        $('#oldVersionAlert').modal('show');
    });
  });
</script>