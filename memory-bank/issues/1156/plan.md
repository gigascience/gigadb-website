# Add filtering functionality to dataset samples table

## User story description
As a user viewing a dataset page, I want to filter the samples table by entering keywords in filter fields so that I can quickly find specific samples based on their attributes.

## User story completion requirements
- [ ] Add input filter fields for Sample ID, Taxonomic ID, Common Name, Genbank Name, Scientific Name, and Sample Attributes
- [ ] Implement case-insensitive partial matching for all filters
- [ ] Enable multiple simultaneous filters (combined with AND logic)
- [ ] Activate filters on Enter key press or input blur event
- [ ] Clear filters when input is empty and Enter key is pressed or input loses focus
- [ ] Add a "Clear All Filters" button to reset all filters at once
- [ ] Implement server-side filtering (not using DataTables' built-in filtering)

## Tasks
- [x] Add filter input fields to the samples table in view.php
  - [x] Add a new row after the header row with input fields for each filterable column
  - [x] Each input field should have appropriate aria-labels for accessibility
  - [x] Style the filters to match the existing table design
  - [x] Add a "Clear All Filters" button above or below the table
  - [x] Implementation:
    ```php
    <tr class="table-filters-row">
        <th><input type="text" class="form-control" aria-label="Filter by Sample ID" /></th>
        <th><input type="text" class="form-control" aria-label="Filter by Common Name" /></th>
        <!-- Add remaining input fields -->
    </tr>
    ```
  - [x] Test by checking the HTML structure and styling of the added filters

- [ ] Implement JavaScript to handle filter UI interactions
  - [x] Add event listeners for Enter key press and blur events on filter inputs
  - [x] Implement filter state management (storing current filter values)
  - [x] Add functionality to clear individual filters and all filters
  - [ ] Send filter parameters to the server via AJAX
  - [ ] Update the table with filtered results
  - [ ] Implementation:
    ```javascript
    $(document).ready(function() {
        const filterInputs = $('.table-filters-row input');
        // Add event handlers for Enter key and blur events
        // Implement AJAX calls to backend with filter parameters
    });
    ```
  - [ ] Test by manually entering filter values and checking UI behavior

- [ ] Modify DatasetController to handle filter parameters
  - [ ] Update actionView method to accept filter parameters from requests
  - [ ] Pass filter criteria to the appropriate model methods
  - [ ] Return filtered results to the view
  - [ ] Implementation:
    - [ ] Add parameter handling for filter values in the actionView method
    - [ ] Pass filter parameters to DatasetPageAssembly when setting up samples
  - [ ] Test by checking network requests and responses when filters are applied

- [ ] Update DatasetPageAssembly to support sample filtering
  - [ ] Modify setDatasetSamples method to accept filter parameters
  - [ ] Pass filter parameters to the appropriate data source
  - [ ] Implementation:
    - [ ] Add filter parameters to the setDatasetSamples method signature
    - [ ] Pass filter parameters to FormattedDatasetSamples and/or StoredDatasetSamples
  - [ ] Test by checking that filter parameters are correctly passed through the assembly

- [ ] Modify FormattedDatasetSamples to handle filtering logic
  - [ ] Update the getDataProvider method to apply filter criteria
  - [ ] Ensure filter parameters are passed to the underlying data source
  - [ ] Implementation:
    - [ ] Add filter handling to the getDataProvider method
    - [ ] Pass filter parameters to getDatasetSamples method
  - [ ] Test by verifying filtered data is returned correctly from the data provider

- [ ] Update StoredDatasetSamples to filter data from the database
  - [ ] Modify the SQL query in getDatasetSamples to include WHERE clauses for filters
  - [ ] Implement case-insensitive partial matching for all filter fields
  - [ ] Handle multiple simultaneous filters with AND logic
  - [ ] Implementation:
    - [ ] Modify the SQL query to include conditional WHERE clauses based on filter parameters
    - [ ] Use ILIKE or LOWER() for case-insensitive matching
  - [ ] Test with database queries to ensure correct filtering is applied

- [ ] Write acceptance tests for the new filtering functionality
  - [ ] Create Codeception tests to verify:
    - [ ] Filter inputs appear on the samples table
    - [ ] Entering filter values correctly filters the table
    - [ ] Multiple filters work together (AND logic)
    - [ ] Clearing filters restores the original table data
    - [ ] "Clear All Filters" button resets all filters
  - [ ] Implementation:
    - [ ] Add new test methods to the appropriate Codeception test class
    - [ ] Test various filter combinations and edge cases
