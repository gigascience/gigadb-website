<?php

    /**
     * @var $sampleDataProvider
     * @var $model
     * @var $columns
     */

    $samplesPerPage = $sampleDataProvider->getItemCount();
    $totalNbSamples = $sampleDataProvider->getTotalItemCount();

    if (count($model->samples) > 0) {
    ?>

<div role="tabpanel" class="tab-pane active" id="sample">
    <p class="pull-left">
    Click on a table column to sort the results.
    </p>
    <div class="btns-row btns-row-end">
        <button id="clear_samples_filters" class="btn btn-default" type="button" onClick="clearFilters()">
            <span class="glyphicon glyphicon-remove"></span> Clear All Filters
        </button>
        <a id="samples_table_settings" class="btn btn-default" data-toggle="modal" data-target="#samples_settings" href="#">
            <span class="glyphicon glyphicon-adjust"></span>Table Settings
        </a>
    </div>
    <div class="clearfix"></div>
    <table id="samples_table" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr class="table-headers-row">
                <th scope="col" title="User-specified name or identifier of the sample object. Note: a DNA sample and an RNA sample from the same donor are classed as two separate samples." style="width: 100px;">Sample ID</th>
                <th scope="col" title="A well recognized commonly used name of the species, usually this is a synonym held in the NCBI taxonomy for the tax ID provided.">Common Name</th>
                <th scope="col" title="The scientific binomial name of the species, usually this is in direct accordance with the NCBI taxonomy ID provided.">Scientific Name</th>
                <th scope="col" title="This is a list of Key:Value pairs, where the Keys are from our Attributes list, and the Value is the specific value for the sample. See our metadata guide for the Attributes list with definitions of all available attributes.">Sample Attributes</th>
                <th scope="col" title="Species taxonomy ID of the sampled species, we currently use the NCBI taxonomy as the source of this identifier.">Taxonomic ID</th>
                <th scope="col" title="The preferred display name used by NCBI taxonomy for the tax ID provided">Genbank Name</th>
            </tr>
            <tr class="table-filters-row">
                <th>
                    <input data-filter="sample_id" type="text" class="form-control" aria-label="Filter by Sample ID" />
                </th>
                <th>
                    <input data-filter="common_name" type="text" class="form-control" aria-label="Filter by Common Name" />
                </th>
                <th>
                    <input data-filter="scientific_name" type="text" class="form-control" aria-label="Filter by Scientific Name" />
                </th>
                <th>
                    <input data-filter="attribute" type="text" class="form-control" aria-label="Filter by Sample Attributes" />
                </th>
                <th>
                    <input data-filter="taxonomic_id" type="text" class="form-control" aria-label="Filter by Taxonomic ID" />
                </th>
                <th>
                    <input data-filter="genbank_name" type="text" class="form-control" aria-label="Filter by Genbank Name" />
                </th>
            </tr>
        </thead>
        <tbody>
            <?php $sample_models = $sampleDataProvider->getData();

                foreach ($sample_models as $sample) {?>
                <tr>
                    <td><?php echo $sample['linkName'] ?></td>
                    <td><?php echo $sample['common_name'] ?></td>
                    <td><?php echo $sample['scientific_name'] ?></td>
                    <td><?php echo $sample['displayAttr'] ?></td>
                    <td><?php echo $sample['taxonomy_link'] ?></td>
                    <td><?php echo $sample['genbank_name'] ?></td>
                </tr>
            <?php }?>

        </tbody>
    </table>
    <div class="table-footer">
        <?php
            if ($samplesPerPage != $totalNbSamples) {
                ?>
        <div class="pagination-wrapper">
        <?
            $this->widget('SiteLinkPager', array(
                'id' => 'samples-pager',
                'pages' => $sampleDataProvider->getPagination(),
            ));
        ?>
        <div class="page-selector">
        <button class="btn background-btn-o" id="samplesPageButton" onclick="onPageChange()">Go to page</button>
        <input type="number" id="samplesPageInput" class="page_box" onkeypress="onPageInputKeyPress(event)" min="1" max="<?php echo $sampleDataProvider->getPagination()->getPageCount() ?>" aria-label="Enter page number">
        <span class="page-selector-label"> of           <?php echo $sampleDataProvider->getPagination()->getPageCount() ?></span>
        </div>
        </div>
        <?php
            }
            ?>
        <div class="pull-right">
            <div class="summary">Displaying <?php echo $samplesPerPage ?> samples of <?php echo $totalNbSamples ?></div>
        </div>
    </div>
</div>
<?php
    }
?>

<script>
    // filter helpers
    function handleFilter() {
        console.log('Enter key pressed on filter input');
        const filterState = getFilterState();
        console.log('Current filter state:', filterState);
    }

    function getFilterState() {
        const filterState = {};
        $('.table-filters-row input').each(function() {
            const input = $(this);
            const filterType = input.attr('data-filter');
            filterState[filterType] = input.val();
        });
        return filterState;
    }

    /**
     * Set filter input values programmatically
     * @param {Object} newFilterState - The new filter state to set
     * @example
     * setFilterState({
     *     sample_id: '123',
     *     common_name: 'Dog',
     *     scientific_name: 'Canis lupus familiaris',
     *     attribute: 'color:brown',
     *     taxonomic_id: '9606',
     *     genbank_name: 'NC_000001.10'
     * });
     * if a property is not provided, it is set to an empty string
     */
    function setFilterState(newFilterState) {
        $('.table-filters-row input').each(function() {
            const input = $(this);
            const filterType = input.attr('data-filter');
            const value = newFilterState[filterType] || '';
            input.val(value);
        });
        console.log('Filter state set:', getFilterState());
    }

    function clearFilters() {
        console.log('Clearing filters');
        setFilterState({});
    }

    // init table
    $(document).ready(function() {
        $('#samples_table').DataTable({
            "initComplete": function () {
                $("#samples_table").wrap("<div class='dataset-datatables-wrapper'></div>");

                // Add event listeners for filter inputs
                $('.table-filters-row input').on('keypress', function(e) {
                    const isEnter =  e.which === 13 || e.keyCode === 13 || e.key === "Enter"
                    if (isEnter) {
                        handleFilter();
                    }
                });
                $('.table-filters-row input').on('blur', function() {
                    handleFilter();
                });
            },
            "paging": false,
            "ordering": true,
            orderCellsTop: true,
            "info": false,
            "searching": false,
            "lengthChange": false,
            "pageLength": <?php echo $sampleDataProvider->getPagination()->getPageSize() ?>,
            "pagingType": "simple_numbers",
            "columns": [{
                    "visible": <?php echo in_array('name', $columns) ? 'true' : 'false' ?>
                },
                {
                    "visible": <?php echo in_array('common_name', $columns) ? 'true' : 'false' ?>
                },
                {
                    "visible": <?php echo in_array('scientific_name', $columns) ? 'true' : 'false' ?>
                },
                {
                    "visible": <?php echo in_array('attribute', $columns) ? 'true' : 'false' ?>
                },
                {
                    "visible": <?php echo in_array('taxonomic_id', $columns) ? 'true' : 'false' ?>
                },
                {
                    "visible": <?php echo in_array('genbank_name', $columns) ? 'true' : 'false' ?>
                },
            ]
        });


        // pagination
        function onPageChange() {
        const params = {
            maxPage: <?php echo $sampleDataProvider->getPagination()->getPageCount() ?>,
            targetPageNumber: document.getElementById('samplesPageInput').value,
            pathPart: 'Samples_page'
        }
        goToPage(params);
        }

        function onPageInputKeyPress(event) {
            const isEnter =  event.which === 13 || event.keyCode === 13 || event.key === "Enter"
            if (isEnter) {
                onPageChange();
            }
        }
    });
</script>