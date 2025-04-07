# Outline

The view protected/views/dataset/view.php displays a dataset samples table that needs filtering functionality.

## Implementation Requirements

### Filter Interface
- Add filter input fields in a new row below the table headers for:
  - Sample ID
  - Taxonomic ID
  - Common Name
  - Genbank Name
  - Scientific Name
  - Sample Attributes
- Add a "Clear All Filters" button above or below the table

### Filter Behavior
- Server-side filtering implementation (not using DataTables built-in filtering)
- All filters are text input fields supporting:
  - Case-insensitive partial matching
  - Multiple simultaneous filters (combined with AND logic)
  - No minimum character requirement
  - No special character validation needed

### Filter Triggers
- Filters activate on:
  - Enter key press while input is focused
  - Input blur event
- Filters clear on:
  - Empty input + Enter key
  - Empty input + blur event
  - "Clear All Filters" button click

### Technical Specifications
- Filter state does not need persistence between page navigations
- Empty/null filter values are ignored in the query
- Server-side implementation will require:
  - Updates to DatasetController::actionView to handle filter requests
  - Modifications to relevant models for filter query handling
  - JavaScript implementation for filter UI interaction

# Relevant files:

## View Files
- `protected/views/dataset/view.php`
  - Main view file displaying the dataset details and samples table
  - Contains the table structure and DataTables integration
  - Needs updates to:
    - Add filter input fields below table headers
    - Add "Clear All Filters" button
    - Implement filter UI interaction

- `protected/views/dataset/_sample_setting.php`
  - Component handling sample table settings and column visibility
  - Contains existing form elements for table configuration
  - Currently manages column visibility and items per page
  - Might not need any updates

## Controller Files
- `protected/controllers/DatasetController.php:actionView`
  - Handles dataset display and data retrieval
  - Manages sample data through DatasetPageAssembly and DatasetPageSettings
  - Needs updates to:
    - Handle filter parameters from requests
    - Pass filter criteria to models
    - Return filtered results to view

## Model Files
- `protected/models/Dataset.php` - Main dataset model
- `protected/models/DatasetDAO.php` - Data access layer for datasets
- `protected/models/DatasetPageSettings.php` - Handles table settings and configuration
- `protected/models/DatasetPageAssembly.php` - Assembles different components of the dataset page

# User stories
Given I am on a dataset page
When I navigate to the samples tab
And I enter a keyword in the "Sample ID" filter
Then the table only show the sample whose "Sample ID" matches what I have entered

Same as the first one, but with "Taxonomic ID", "Common Name", "Genbank Name", "Scientific Name" and "Sample Attributes"
