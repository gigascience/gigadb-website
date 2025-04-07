# Server-side Pagination for Samples Table - 2025-04-05

## Feature Summary
The server-side pagination feature allows users to navigate through large sets of sample data in a dataset by loading only a subset of records at a time. This improves performance and reduces memory usage by not loading all samples at once.

## Key Components

### 1. FormattedDatasetSamples (`protected/components/FormattedDatasetSamples.php`)
- Main adapter class that handles the presentation and pagination of samples
- Implements `DatasetSamplesInterface`
- Uses `CPagination` for pagination management

### 2. DatasetPageAssembly (`protected/components/DatasetPageAssembly.php`)
- Factory class that assembles the dataset page components
- Creates and configures the pagination components
- Sets up the data sources with caching support

### 3. view.php (`protected/views/dataset/view.php`)
- View template that renders the samples table with pagination controls
- Integrates with DataTables for client-side table features
- Handles pagination UI and navigation

### 4. FilesPagination (`protected/components/FilesPagination.php`)
- Custom extension of Yii's `CPagination` class
- Modifies URL generation for pagination links

## Implementation Workflow

### 1. Initial Setup
```php
// In DatasetPageAssembly.php
public function setDatasetSamples(int $pageSize): DatasetPageAssembly {
    // Create data source with caching
    $dataSource = new CachedDatasetSamples(
        $this->_app->cache,
        $this->_cacheDependency,
        new StoredDatasetSamples(
            $this->_dataset->id,
            $this->_app->db
        )
    );

    // Configure pagination
    $pager = new FilesPagination();
    $pager->setPageSize($pageSize);
    $this->_samples = new FormattedDatasetSamples($pager, $dataSource);
    return $this;
}
```

### 2. Data Retrieval and Pagination
```php
// In FormattedDatasetSamples.php
public function getDataProvider(): CArrayDataProvider {
    // Get total count for pagination
    $totalSampleCount = $this->countDatasetSamples();
    $this->pager->setItemCount($totalSampleCount);
    $this->pager->pageVar = "Samples_page";

    // Calculate offset based on current page
    $currentPage = $this->pager->getCurrentPage();
    $nbToSkip = $currentPage * $this->pager->getPageSize();

    // Get paginated data
    $samples = $this->getDatasetSamples($this->pager->getPageSize(), $nbToSkip);

    // Configure data provider
    $dataProvider = new CArrayDataProvider(null, [
        'totalItemCount' => $totalSampleCount,
        'sort' => [
            'defaultOrder' => 't.name ASC',
            'attributes' => [
                'name',
                'common_name',
                'genbank_name',
                'scientific_name',
                'tax_id',
            ]
        ],
        'pagination' => null
    ]);

    $dataProvider->setPagination($this->pager);
    $dataProvider->setData($samples);
    return $dataProvider;
}
```

### 3. View Rendering
```php
// In view.php
<?php if ($sampleDataProvider->getTotalItemCount() > 0) { ?>
    <div role="tabpanel" class="tab-pane active" id="sample">
        <table id="samples_table" class="table table-striped table-bordered">
            <!-- Table headers -->
            <tbody>
                <?php
                $sample_models = $sampleDataProvider->getData();
                foreach ($sample_models as $sample) { ?>
                    <tr>
                        <td><?= $sample['linkName'] ?></td>
                        <!-- Other columns -->
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Pagination controls -->
        <div class="pagination-wrapper">
            <?php
            $this->widget('SiteLinkPager', array(
                'id' => 'samples-pager',
                'pages' => $sampleDataProvider->getPagination(),
            ));
            ?>
            <div class="page-selector">
                <button class="btn background-btn-o" onclick="goToSamplesPage()">
                    Go to page
                </button>
                <input type="number" id="samplesPageInput" min="1"
                       max="<?= $sampleDataProvider->getPagination()->getPageCount() ?>">
            </div>
        </div>
    </div>
<?php } ?>
```

### 4. Client-side Navigation
```javascript
function goToSamplesPage() {
    const pageID = <?php echo $model->identifier ?>;
    const max = <?php echo $sampleDataProvider->getPagination()->getPageCount() ?>;
    const min = 1;
    let targetPageNumber = document.getElementById("samplesPageInput").value;
    const userInput = parseInt(targetPageNumber);
    const targetUrlArray = ["", "dataset", "view", "id", pageID];

    targetUrlArray.push('Samples_page', computePageNumber(userInput, min, max));
    window.location = window.location.origin + targetUrlArray.join("/");
}
```

## Data Flow
1. User requests a dataset page or clicks pagination controls
2. Server calculates the required page offset and limit
3. `FormattedDatasetSamples` retrieves the paginated data from the database
4. Data is cached if caching is enabled
5. View renders the current page of samples with pagination controls
6. User can navigate using page numbers or direct page input

## Key Dependencies
1. Yii Framework's pagination components (`CPagination`)
2. DataTables library for table features
3. jQuery for client-side interactions
4. Server-side caching system for performance optimization
5. Database connection for data retrieval

The implementation follows a clean separation of concerns with the adapter pattern and provides both server-side pagination for efficient data loading and client-side features for a smooth user experience.