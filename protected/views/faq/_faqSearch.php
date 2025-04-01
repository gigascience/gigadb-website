<div class="faq-search-container">
    <div class="form-group">
        <label for="faqSearch">Search our frequently asked questions</label>
        <div class="faq-search-controls">
            <i class="fa fa-search search-icon" aria-hidden="true"></i>
            <input
                type="search"
                class="form-control"
                id="faqSearch"
                name="faqSearch"
                placeholder="Enter here your search query..."
                aria-controls="accordion"
            >
            <button
                class="btn background-btn-o"
                type="button"
                id="clearSearch"
                aria-label="Clear FAQ search"
            >
                Clear
            </button>
        </div>
        <div id="searchResults" role="status" class="sr-only"></div>
    </div>
</div>

<?php
Yii::app()->assetManager->forceCopy = YII_DEBUG;
$jsDir = Yii::getAlias('/gigadb/app/client/js');
$jsUrl = Yii::app()->assetManager->publish($jsDir);

Yii::app()->clientScript->registerScriptFile($jsUrl . '/utils/debounce.js', CClientScript::POS_END, ['type' => 'module']);
?>

<script type="module">
    import { debounce } from "<?php echo $jsUrl; ?>/utils/debounce.js";

    $(document).ready(function() {
        const $faqSearch = $('#faqSearch');
        const $clearButton = $('#clearSearch');
        const $panels = $('.panel');
        const $searchResults = $('#searchResults');

        const clearSearch = () => {
            $faqSearch.val('').trigger('input');
        };

        const searchHandler = function(event) {
            const searchText = $(event.target).val().toLowerCase();
            let visibleCount = 0;

            // collapse all panels
            $panels.find('.panel-collapse').collapse('hide');

            // handle panel visibility
            $panels.each(function() {
                const $panel = $(this);

                // Hide panel by default
                $panel.hide();

                // Always show contact form panel
                if ($panel.hasClass('js-panel-always-visible')) {
                    $panel.show();
                    return;
                }

                const questionText = $panel.find('.panel-title').text().toLowerCase();
                const answerText = $panel.find('.panel-body').text().toLowerCase();

                if (`${questionText} ${answerText}`.includes(searchText)) {
                    $panel.show();
                    visibleCount++;
                }
            });

            // update sr-only message
            if (searchText.length === 0) {
                $searchResults.text('Search cleared, showing all questions');
            } else {
                const resultText = visibleCount === 0
                    ? 'No matching questions found'
                    : `Found ${visibleCount} matching question${visibleCount === 1 ? '' : 's'}`;
                $searchResults.text(resultText);
            }

            // handle panel expanded / collapse state
            const visiblePanels = $panels.filter(':visible')

            if (visiblePanels.length === 1) {
                visiblePanels.first().find('.panel-collapse').collapse('show');
            } else if (visiblePanels.length === 2) {
                visiblePanels.first().find('.panel-collapse').collapse('show');
            }
        }

        const debouncedSearchHandler = debounce(searchHandler, 400);

        $faqSearch.on('input', debouncedSearchHandler);
        $clearButton.on('click', clearSearch);

        $faqSearch.on('keydown', function(e) {
            if (e.key === "Escape") {
                clearSearch();
            }
        });
    });
</script>