<?php
/**
 * Partial: Print View Toggle
 *
 * Renders a button that lets the user switch between the regular “web” view
 * and a printer-friendly version of the current page. The script embedded in
 * this partial inspects the current URL for the query parameter
 * `print=true`, toggles a `print` class on the <body> element, and updates the
 * button’s label accordingly. Pressing the button flips the state, modifies
 * the URL via `history.replaceState`, and reapplies the class without
 * reloading the page.
 *
 * Usage (Yii 1.1):
 *     <?php $this->renderPartial('shared/_printToggle'); ?>
 *
 * Requirements:
 *   - Pages should provide CSS rules that target `body.print`.
 */
?>
<button class="btn btn-link print-view-link" aria-label="Switch to print view">
    <span class="label-text">Print view</span>
</button>
<script>
    $(document).ready(function () {
        const url = new URL(window.location);
        const $btn = $('.print-view-link');
        const $label = $btn.find('.label-text');

        const update = (toPrint) => {
            $('body').toggleClass('print', toPrint);
            $label.text(toPrint ? 'Web view' : 'Print view');
        };

        let isPrint = url.searchParams.get('print') === 'true';
        update(isPrint);

        $btn.on('click', () => {
            isPrint = !isPrint;
            if (isPrint) {
                url.searchParams.set('print', 'true');
            } else {
                url.searchParams.delete('print');
            }
            history.replaceState(null, '', url);
            update(isPrint);
        });
    });
</script>
