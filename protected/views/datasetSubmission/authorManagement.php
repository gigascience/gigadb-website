<h2>Add Authors</h2>
<div class="clear"></div>

<?php $this->renderPartial('_tabs_navigation', array('model' => $model)); ?>

<div class="span12 form well">
    <div class="form-horizontal">
        <div id="author-grid" class="grid-view">
            <p>Please provide all author details, to do this you may add them individually, or upload a CSV file. Once added the author details will appear in the table below and you may make any required changes directly in the table.</p>
            <table class="table table-bordered" id="author-table">
                <thead>
                <tr>
                    <th id="author-grid_c0">First name</th>
                    <th id="author-grid_c1">
                        <span>Middle name</span>
                        <a class="myHint"
                           data-content='Enter all middle names or initials separated by spaces, the initial of each middle name will be used in the displayed name.'
                           style="float: right">
                        </a>
                    </th>
                    <th id="author-grid_c2">Last name</th>
                    <th id="author-grid_c3">
                        <span>ORCiD</span>
                        <a class="myHint"
                           data-content='<a href=https://orcid.org/about/what-is-orcid/mission target=_blank>ORCID<a/> provides a persistent digital identifier that distinguishes you from every other researcher.'
                           data-html="true"
                           style="float: right;">
                        </a>
                    </th>
                    <th id="author-grid_c4">CrediT</th>
                    <th id="author-grid_c5">
                        <span>Order</span>
                        <a class="myHint"
                           data-content="This is the order in which authors will appear in the dataset citation."
                           style="float: right;"></a>
                    </th>
                    <th id="author-grid_c6" class="button-column" width="5%"></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($das as $da): ?>
                    <tr class="odd">
                        <td><?=$da->author->first_name?></td>
                        <td><?=$da->author->middle_name?></td>
                        <td><?=$da->author->surname?></td>
                        <td><?=$da->author->orcid?></td>
                        <td><?=$da->contribution ? $da->contribution->name : '' ?></td>
                        <td>
                            <input class='js-author-rank'
                                   id="js-author-rank-<?=$da->id?>"
                                   da-id="<?=$da->id?>"
                                   value="<?=$da->rank?>"
                                   type="text"
                                   style="width:25px">
                        </td>
                        <td class="button-column">
                            <input type="hidden" class="js-da-id" value="<?= $da->id ?>">
                            <a class="js-delete-author delete-title" da-id="<?= $da->id ?>"  title="delete this row">
                                <img alt="delete this row" src="/images/delete.png">
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr id="no-results"<?php if ($das): ?> style="display: none;"<?php endif ?>>
                    <td colspan="7">
                        <span class="empty">No results found.</span>
                        <input type="hidden" value="999999998" class="js-author-rank">
                    </td>
                </tr>
                <tr id="authors-form">
                    <td>
                        <input id="js-author-first-name" class="js-author-required" type="text" name="Author[first_name]" placeholder="First Name" style="width:150px">
                    </td>
                    <td>
                        <input id="js-author-middle-name" type="text" name="Author[middle_name]" placeholder="Middle Name (optional)" style="width:150px">
                    </td>
                    <td>
                        <input id="js-author-last-name" class="js-author-required" type="text" name="Author[last_name]" placeholder="Last Name" style="width:150px">
                    </td>
                    <td>
                        <input id="js-author-orcid" type="text" pattern="[1-9]{4}-[1-9]{4}-[1-9]{4}-[1-9]{4}" name="Author[orcid]" placeholder="ORCiD (optional)" style="width:130px">
                    </td>
                    <td>
                        <input id="js-author-contribution" class="js-author-required" type="text" name="Author[contribution]" placeholder="Contribution" style="width:120px">
                    </td>
                    <td colspan="2">
                        <input type="hidden" value="999999999" class="js-author-rank">
                        <a href="#" dataset-id="<?=$model->id?>" class="btn js-not-allowed" id="js-add-author"/>Add Author</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <p style="text-align: center">OR</p>

        <div class="add-author-container">
            <label for="authors">author list upload</label>
            <a class="myHint" data-content="You may upload a tabular file of authors names (in TSV or CSV format), please use include 5 columns and 1 row for each author, e.g.<br>
Firstname	Middlename	Lastname	ORCID 		contribution<br>
Rosalind	Elsie	Franklin 	0000-0000-0000-0001	Conceptualization"
               data-html="true" style="float: none"></a>
            <input type="file" id="authors" name="authors">
            <a href="#" dataset-id="<?=$model->id?>" class="btn js-not-allowed" id="js-add-authors"/>Add Authors</a>
        </div>
    </div>

    <div style="text-align:center">
        <a href="/datasetSubmission/create1/id/<?= $model->id ?>" class="btn-green">Previous</a>
        <a href="/datasetSubmission/authorManagement/id/<?= $model->id ?>" class="btn-green js-save-authors">Save</a>
        <a href="/datasetSubmission/additionalManagement/id/<?= $model->id ?>" class="btn-green js-save-authors">Next</a>
    </div>

</div>

<script>
    var contributions = JSON.parse('<?= json_encode(array_values(CHtml::listData($contributions, 'id', 'name'))) ?>');
    var deleteIds = [];
    var dataset_id = <?= $model->id ?>;

    $( "#js-author-contribution" ).autocomplete({
        source: contributions
    });

    $(document).on('change', '.js-author-required', function () {
        makeAddAuthorActiveIfCan();
    });

    $(document).on('change', '#authors', function () {
        if ($(this).val()) {
            $('#js-add-authors').removeClass('js-not-allowed').addClass('btn-green js-add-authors');
        } else {
            $('#js-add-authors').removeClass('btn-green js-add-authors').addClass('js-not-allowed');
        }
    });

    $(document).on('focusin', ".js-author-rank", function(){
        $(this).data('val', $(this).val());
    });

    $(document).on('change', ".js-author-rank", function(e) {
        var input = $(this);
        input.addClass('my-current-input');

        var min = 1;
        var max = $('#author-table').find('.odd').length;

        var val = parseInt(input.val());
        if (!val) val = 0;

        var oldVal = input.data('val');

        if (val < min) {
            val = min;
            alert('Min Order is: ' + min);
        }

        if (val > max) {
            val = max;
            alert('Max Order is: ' + max);
        }

        input.val(val);

        var inc = oldVal < val;

        var inputs = $('.js-author-rank').not('.my-current-input');
        inputs.each(function() {
            let currentVal = parseInt($(this).val());
            if (inc && currentVal > oldVal && currentVal <= val) {
                $(this).val(currentVal - 1);
            } else if (!inc && currentVal < oldVal && currentVal >= val) {
                $(this).val(currentVal + 1);
            }
        });


        $(this).data('val', val);
        input.removeClass('my-current-input');

        tableSort();
        return false;
    });

    function createAuthorTr(author) {
        var order = $('#author-table').find('.odd').length + 1;

        return '<tr class="odd">' +
            '<td>' + author.first_name + '</td>' +
            '<td>' + author.middle_name + '</td>' +
            '<td>' + author.last_name + '</td>' +
            '<td>' + author.orcid + '</td>' +
            '<td>' + author.contribution + '</td>' +
            '<td><input type="text" class="js-author-rank" style="width:25px" value="' + order + '">' + '</td>' +
            '<td class="button-column">' +
            '<a class="js-delete-author delete-title" title="delete this row">' +
            '<img alt="delete this row" src="/images/delete.png">' +
            '</a>' +
            '</td>' +
            '</tr>';
    }

    function tableSort() {
        var table = $('#author-table');
        var tbody = table.find('tbody');
        var rows = tbody.find('tr').toArray().sort(comparer(0));
        for (var i = 0; i < rows.length; i++){table.append(rows[i])}


        function comparer(index) {
            return function(a, b) {
                var valA = getCellValue(a, index), valB = getCellValue(b, index)
                return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB)
            }
        }
        function getCellValue(row, index){
            return $(row).find('.js-author-rank').val()
        }
    }

    function saveAuthors(url) {
        var authors = [];

        var trs = $('#author-table').find('.odd');

        trs.each(function() {
            let tr = $(this);
            let id = tr.find('.js-da-id').val();
            if (!id) {
                id = 0;
            }
            let first_name = tr.children('td').eq(0).text();
            let middle_name = tr.children('td').eq(1).text();
            let last_name = tr.children('td').eq(2).text();
            let orcid = tr.children('td').eq(3).text();
            let contribution = tr.children('td').eq(4).text();
            let order = tr.find('.js-author-rank').val();

            authors.push({
                id: id,
                first_name: first_name,
                middle_name: middle_name,
                last_name: last_name,
                orcid: orcid,
                contribution: contribution,
                order: order
            });
        });

        $.ajax({
            type: 'POST',
            url: '/datasetSubmission/saveAuthors',
            data:{
                'dataset_id': dataset_id,
                'authors':authors,
                'delete_ids':deleteIds,
            },
            success: function(response){
                if(!response.success) {
                    alert(response.message);
                } else {
                    window.location.href = url;
                }
            },
            error: function(xhr) {
                alert(xhr.responseText);
            }
        });
    }

    $(".js-save-authors").click(function() {
        saveAuthors($(this).attr('href'));

        return false;
    });

    $(document).on('click', ".js-add-author", function(e) {
        e.preventDefault();
        var  did = $(this).attr('dataset-id');
        var first_name = $('#js-author-first-name').val();
        var last_name = $('#js-author-last-name').val();
        var middle_name = $('#js-author-middle-name').val();
        var orcid = $('#js-author-orcid').val();
        var contribution = $('#js-author-contribution').val();

        var author = {
            'first_name': first_name,
            'last_name': last_name,
            'middle_name': middle_name,
            'orcid': orcid,
            'contribution': contribution,
        };

        $.ajax({
            type: 'POST',
            url: '/datasetSubmission/validateAuthor',
            data:{'dataset_id': did, 'Author':author},
            success: function(response){
                if(response.success) {
                    var tr = createAuthorTr(response.author);
                    $('#authors-form').before(tr);
                    $('#no-results').hide();

                    $('#js-author-first-name').val('');
                    $('#js-author-middle-name').val('');
                    $('#js-author-last-name').val('');
                    $('#js-author-orcid').val('');
                    $('#js-author-contribution').val('');

                    $('#js-add-author').removeClass('btn-green js-add-author').addClass('js-not-allowed');
                } else {
                    alert(response.message);

                }
            },
            error: function(xhr) {
                alert(xhr.responseText);
            }
        });
    });

    $(document).on('click', ".js-add-authors", function(e) {
        var  did = $(this).attr('dataset-id');

        var data = new FormData();
        data.append("authors", $("#authors")[0].files[0]);
        data.append("dataset_id", did);

        $.ajax({
            url: '/datasetSubmission/addAuthors',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success: function(response){
                if(response.success) {
                    for (var i in response.authors) {
                        var tr = createAuthorTr(response.authors[i]);
                        $('#authors-form').before(tr);
                    }

                    $('#no-results').hide();

                    $("#authors").val('');
                    $('#js-add-authors').removeClass('btn-green js-add-authors').addClass('js-not-allowed');
                } else {
                    alert(response.message);

                }
            },
            error: function(xhr) {
                alert(xhr.responseText);
            }
        });

        return false;
    });

    $(document).on("click", ".js-delete-author", function() {
        if (!confirm('Are you sure you want to delete this item?')) {
            return false;
        }

        var  daid = $(this).attr('da-id');

        if (daid) {
            deleteIds.push(daid);
        }

        var tr = $(this).closest('tr');

        var trs = tr.nextAll('tr.odd');
        if (trs.length) {
            trs.each(function() {
                var rank = $(this).find('.js-author-rank');
                rank.val(rank.val() - 1);
            });
        }

        tr.remove();

        if ($('#author-table').find('.odd').length === 0) {
            $('#no-results').show();
        }

        return false;
    });

    function makeAddAuthorActiveIfCan() {
        if (
            $('#js-author-first-name').val()
            && $('#js-author-last-name').val()
            && $('#js-author-contribution').val()
        ) {
            $('#js-add-author').removeClass('js-not-allowed').addClass('btn-green js-add-author');
        } else {
            $('#js-add-author').removeClass('btn-green js-add-author').addClass('js-not-allowed');
        }
    }

    $("#js-author-orcid").keypress(function(){
        var input = $(this);

        setTimeout((function(){
            var val = input.val();
            var valLength = val.length;

            var lastChar = val.slice(-1);

            if (valLength > 19) {
                input.val(val.slice(0, 19));
                return false;
            }

            if (valLength == 5 || valLength == 10 || valLength == 15) {
                if (lastChar == parseInt(lastChar)) {
                    lastChar = '-' + lastChar;
                } else {
                    lastChar = lastChar.replace(/[^-]/g, '');
                }
            } else {
                lastChar = lastChar.replace(/[^0-9]/g, '');
            }

            var withoutLastChar = val.slice(0, -1);
            withoutLastChar = withoutLastChar.replace(/[^0-9\-]/g, '');
            input.val(withoutLastChar + lastChar);

        }), 50);
    });
</script>